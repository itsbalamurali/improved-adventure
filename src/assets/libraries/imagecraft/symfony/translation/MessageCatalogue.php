<?php



/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Translation;

use Symfony\Component\Config\Resource\ResourceInterface;

/**
 * @author Fabien Potencier <fabien@symfony.com>
 */
class MessageCatalogue implements MessageCatalogueInterface, MetadataAwareInterface
{
    private $messages = [];
    private $metadata = [];
    private $resources = [];
    private $locale;
    private $fallbackCatalogue;
    private $parent;

    /**
     * @param string $locale   The locale
     * @param array  $messages An array of messages classified by domain
     */
    public function __construct($locale, array $messages = [])
    {
        $this->locale = $locale;
        $this->messages = $messages;
    }

    public function getLocale()
    {
        return $this->locale;
    }

    public function getDomains()
    {
        return array_keys($this->messages);
    }

    public function all($domain = null)
    {
        if (null === $domain) {
            return $this->messages;
        }

        return $this->messages[$domain] ?? [];
    }

    public function set($id, $translation, $domain = 'messages'): void
    {
        $this->add([$id => $translation], $domain);
    }

    public function has($id, $domain = 'messages')
    {
        if (isset($this->messages[$domain][$id])) {
            return true;
        }

        if (null !== $this->fallbackCatalogue) {
            return $this->fallbackCatalogue->has($id, $domain);
        }

        return false;
    }

    public function defines($id, $domain = 'messages')
    {
        return isset($this->messages[$domain][$id]);
    }

    public function get($id, $domain = 'messages')
    {
        if (isset($this->messages[$domain][$id])) {
            return $this->messages[$domain][$id];
        }

        if (null !== $this->fallbackCatalogue) {
            return $this->fallbackCatalogue->get($id, $domain);
        }

        return $id;
    }

    public function replace($messages, $domain = 'messages'): void
    {
        $this->messages[$domain] = [];

        $this->add($messages, $domain);
    }

    public function add($messages, $domain = 'messages'): void
    {
        if (!isset($this->messages[$domain])) {
            $this->messages[$domain] = $messages;
        } else {
            $this->messages[$domain] = array_replace($this->messages[$domain], $messages);
        }
    }

    public function addCatalogue(MessageCatalogueInterface $catalogue): void
    {
        if ($catalogue->getLocale() !== $this->locale) {
            throw new \LogicException(sprintf('Cannot add a catalogue for locale "%s" as the current locale for this catalogue is "%s"', $catalogue->getLocale(), $this->locale));
        }

        foreach ($catalogue->all() as $domain => $messages) {
            $this->add($messages, $domain);
        }

        foreach ($catalogue->getResources() as $resource) {
            $this->addResource($resource);
        }

        if ($catalogue instanceof MetadataAwareInterface) {
            $metadata = $catalogue->getMetadata('', '');
            $this->addMetadata($metadata);
        }
    }

    public function addFallbackCatalogue(MessageCatalogueInterface $catalogue): void
    {
        // detect circular references
        $c = $catalogue;
        while ($c = $c->getFallbackCatalogue()) {
            if ($c->getLocale() === $this->getLocale()) {
                throw new \LogicException(sprintf('Circular reference detected when adding a fallback catalogue for locale "%s".', $catalogue->getLocale()));
            }
        }

        $c = $this;
        do {
            if ($c->getLocale() === $catalogue->getLocale()) {
                throw new \LogicException(sprintf('Circular reference detected when adding a fallback catalogue for locale "%s".', $catalogue->getLocale()));
            }

            foreach ($catalogue->getResources() as $resource) {
                $c->addResource($resource);
            }
        } while ($c = $c->parent);

        $catalogue->parent = $this;
        $this->fallbackCatalogue = $catalogue;

        foreach ($catalogue->getResources() as $resource) {
            $this->addResource($resource);
        }
    }

    public function getFallbackCatalogue()
    {
        return $this->fallbackCatalogue;
    }

    public function getResources()
    {
        return array_values($this->resources);
    }

    public function addResource(ResourceInterface $resource): void
    {
        $this->resources[$resource->__toString()] = $resource;
    }

    public function getMetadata($key = '', $domain = 'messages')
    {
        if ('' === $domain) {
            return $this->metadata;
        }

        if (isset($this->metadata[$domain])) {
            if ('' === $key) {
                return $this->metadata[$domain];
            }

            if (isset($this->metadata[$domain][$key])) {
                return $this->metadata[$domain][$key];
            }
        }
    }

    public function setMetadata($key, $value, $domain = 'messages'): void
    {
        $this->metadata[$domain][$key] = $value;
    }

    public function deleteMetadata($key = '', $domain = 'messages'): void
    {
        if ('' === $domain) {
            $this->metadata = [];
        } elseif ('' === $key) {
            unset($this->metadata[$domain]);
        } else {
            unset($this->metadata[$domain][$key]);
        }
    }

    /**
     * Adds current values with the new values.
     *
     * @param array $values Values to add
     */
    private function addMetadata(array $values): void
    {
        foreach ($values as $domain => $keys) {
            foreach ($keys as $key => $value) {
                $this->setMetadata($key, $value, $domain);
            }
        }
    }
}
