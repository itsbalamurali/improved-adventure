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

use Symfony\Component\Config\ConfigCacheFactory;
use Symfony\Component\Config\ConfigCacheFactoryInterface;
use Symfony\Component\Config\ConfigCacheInterface;
use Symfony\Component\Translation\Exception\NotFoundResourceException;
use Symfony\Component\Translation\Loader\LoaderInterface;

/**
 * @author Fabien Potencier <fabien@symfony.com>
 */
class Translator implements TranslatorInterface, TranslatorBagInterface
{
    /**
     * @var MessageCatalogueInterface[]
     */
    protected $catalogues = [];

    /**
     * @var string
     */
    protected $locale;

    /**
     * @var array
     */
    private $fallbackLocales = [];

    /**
     * @var LoaderInterface[]
     */
    private $loaders = [];

    /**
     * @var array
     */
    private $resources = [];

    /**
     * @var MessageSelector
     */
    private $selector;

    /**
     * @var string
     */
    private $cacheDir;

    /**
     * @var bool
     */
    private $debug;

    /**
     * @var null|ConfigCacheFactoryInterface
     */
    private $configCacheFactory;

    /**
     * @param string               $locale   The locale
     * @param null|MessageSelector $selector The message selector for pluralization
     * @param null|string          $cacheDir The directory to use for the cache
     * @param bool                 $debug    Use cache in debug mode ?
     *
     * @throws \InvalidArgumentException If a locale contains invalid characters
     */
    public function __construct($locale, ?MessageSelector $selector = null, $cacheDir = null, $debug = false)
    {
        $this->setLocale($locale);
        $this->selector = $selector ?: new MessageSelector();
        $this->cacheDir = $cacheDir;
        $this->debug = $debug;
    }

    public function setConfigCacheFactory(ConfigCacheFactoryInterface $configCacheFactory): void
    {
        $this->configCacheFactory = $configCacheFactory;
    }

    /**
     * Adds a Loader.
     *
     * @param string          $format The name of the loader (@see addResource())
     * @param LoaderInterface $loader A LoaderInterface instance
     */
    public function addLoader($format, LoaderInterface $loader): void
    {
        $this->loaders[$format] = $loader;
    }

    /**
     * Adds a Resource.
     *
     * @param string $format   The name of the loader (@see addLoader())
     * @param mixed  $resource The resource name
     * @param string $locale   The locale
     * @param string $domain   The domain
     *
     * @throws \InvalidArgumentException If the locale contains invalid characters
     */
    public function addResource($format, $resource, $locale, $domain = null): void
    {
        if (null === $domain) {
            $domain = 'messages';
        }

        $this->assertValidLocale($locale);

        $this->resources[$locale][] = [$format, $resource, $domain];

        if (\in_array($locale, $this->fallbackLocales, true)) {
            $this->catalogues = [];
        } else {
            unset($this->catalogues[$locale]);
        }
    }

    public function setLocale($locale): void
    {
        $this->assertValidLocale($locale);
        $this->locale = $locale;
    }

    public function getLocale()
    {
        return $this->locale;
    }

    /**
     * Sets the fallback locale(s).
     *
     * @param array|string $locales The fallback locale(s)
     *
     * @throws \InvalidArgumentException If a locale contains invalid characters
     *
     * @deprecated since version 2.3, to be removed in 3.0. Use setFallbackLocales() instead
     */
    public function setFallbackLocale($locales): void
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since Symfony 2.3 and will be removed in 3.0. Use the setFallbackLocales() method instead.', E_USER_DEPRECATED);

        $this->setFallbackLocales(\is_array($locales) ? $locales : [$locales]);
    }

    /**
     * Sets the fallback locales.
     *
     * @param array $locales The fallback locales
     *
     * @throws \InvalidArgumentException If a locale contains invalid characters
     */
    public function setFallbackLocales(array $locales): void
    {
        // needed as the fallback locales are linked to the already loaded catalogues
        $this->catalogues = [];

        foreach ($locales as $locale) {
            $this->assertValidLocale($locale);
        }

        $this->fallbackLocales = $locales;
    }

    /**
     * Gets the fallback locales.
     *
     * @return array The fallback locales
     */
    public function getFallbackLocales()
    {
        return $this->fallbackLocales;
    }

    public function trans($id, array $parameters = [], $domain = null, $locale = null)
    {
        if (null === $domain) {
            $domain = 'messages';
        }

        return strtr($this->getCatalogue($locale)->get((string) $id, $domain), $parameters);
    }

    public function transChoice($id, $number, array $parameters = [], $domain = null, $locale = null)
    {
        if (null === $domain) {
            $domain = 'messages';
        }

        $id = (string) $id;
        $catalogue = $this->getCatalogue($locale);
        $locale = $catalogue->getLocale();
        while (!$catalogue->defines($id, $domain)) {
            if ($cat = $catalogue->getFallbackCatalogue()) {
                $catalogue = $cat;
                $locale = $catalogue->getLocale();
            } else {
                break;
            }
        }

        return strtr($this->selector->choose($catalogue->get($id, $domain), (int) $number, $locale), $parameters);
    }

    public function getCatalogue($locale = null)
    {
        if (null === $locale) {
            $locale = $this->getLocale();
        } else {
            $this->assertValidLocale($locale);
        }

        if (!isset($this->catalogues[$locale])) {
            $this->loadCatalogue($locale);
        }

        return $this->catalogues[$locale];
    }

    /**
     * Collects all messages for the given locale.
     *
     * @param null|string $locale Locale of translations, by default is current locale
     *
     * @return array[array] indexed by catalog
     *
     * @deprecated since version 2.8, to be removed in 3.0. Use TranslatorBagInterface::getCatalogue() method instead.
     */
    public function getMessages($locale = null)
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since Symfony 2.8 and will be removed in 3.0. Use TranslatorBagInterface::getCatalogue() method instead.', E_USER_DEPRECATED);

        $catalogue = $this->getCatalogue($locale);
        $messages = $catalogue->all();
        while ($catalogue = $catalogue->getFallbackCatalogue()) {
            $messages = array_replace_recursive($catalogue->all(), $messages);
        }

        return $messages;
    }

    /**
     * This method is public because it needs to be callable from a closure in PHP 5.3. It should be made protected (or even private, if possible) in 3.0.
     *
     * @internal
     *
     * @param mixed $locale
     */
    public function dumpCatalogue($locale, ConfigCacheInterface $cache): void
    {
        $this->initializeCatalogue($locale);
        $fallbackContent = $this->getFallbackContent($this->catalogues[$locale]);

        $content = sprintf(
            <<<'EOF'
                <?php

                use Symfony\Component\Translation\MessageCatalogue;

                $catalogue = new MessageCatalogue('%s', %s);

                %s
                return $catalogue;

                EOF
            ,
            $locale,
            var_export($this->catalogues[$locale]->all(), true),
            $fallbackContent
        );

        $cache->write($content, $this->catalogues[$locale]->getResources());
    }

    /**
     * Gets the loaders.
     *
     * @return array LoaderInterface[]
     */
    protected function getLoaders()
    {
        return $this->loaders;
    }

    /**
     * @param string $locale
     */
    protected function loadCatalogue($locale): void
    {
        if (null === $this->cacheDir) {
            $this->initializeCatalogue($locale);
        } else {
            $this->initializeCacheCatalogue($locale);
        }
    }

    /**
     * @param string $locale
     */
    protected function initializeCatalogue($locale): void
    {
        $this->assertValidLocale($locale);

        try {
            $this->doLoadCatalogue($locale);
        } catch (NotFoundResourceException $e) {
            if (!$this->computeFallbackLocales($locale)) {
                throw $e;
            }
        }
        $this->loadFallbackCatalogues($locale);
    }

    protected function computeFallbackLocales($locale)
    {
        $locales = [];
        foreach ($this->fallbackLocales as $fallback) {
            if ($fallback === $locale) {
                continue;
            }

            $locales[] = $fallback;
        }

        if (false !== strrchr($locale, '_')) {
            array_unshift($locales, substr($locale, 0, -\strlen(strrchr($locale, '_'))));
        }

        return array_unique($locales);
    }

    /**
     * Asserts that the locale is valid, throws an Exception if not.
     *
     * @param string $locale Locale to tests
     *
     * @throws \InvalidArgumentException If the locale contains invalid characters
     */
    protected function assertValidLocale($locale): void
    {
        if (1 !== preg_match('/^[a-z0-9@_\\.\\-]*$/i', $locale)) {
            throw new \InvalidArgumentException(sprintf('Invalid "%s" locale.', $locale));
        }
    }

    /**
     * @param string $locale
     */
    private function initializeCacheCatalogue($locale): void
    {
        if (isset($this->catalogues[$locale])) {
            // Catalogue already initialized.
            return;
        }

        $this->assertValidLocale($locale);
        $self = $this; // required for PHP 5.3 where "$this" cannot be use()d in anonymous functions. Change in Symfony 3.0.
        $cache = $this->getConfigCacheFactory()->cache(
            $this->getCatalogueCachePath($locale),
            static function (ConfigCacheInterface $cache) use ($self, $locale): void {
                $self->dumpCatalogue($locale, $cache);
            }
        );

        if (isset($this->catalogues[$locale])) {
            // Catalogue has been initialized as it was written out to cache.
            return;
        }

        // Read catalogue from cache.
        $this->catalogues[$locale] = include $cache->getPath();
    }

    private function getFallbackContent(MessageCatalogue $catalogue)
    {
        $fallbackContent = '';
        $current = '';
        $replacementPattern = '/[^a-z0-9_]/i';
        $fallbackCatalogue = $catalogue->getFallbackCatalogue();
        while ($fallbackCatalogue) {
            $fallback = $fallbackCatalogue->getLocale();
            $fallbackSuffix = ucfirst(preg_replace($replacementPattern, '_', $fallback));
            $currentSuffix = ucfirst(preg_replace($replacementPattern, '_', $current));

            $fallbackContent .= sprintf(
                <<<'EOF'
                    $catalogue%s = new MessageCatalogue('%s', %s);
                    $catalogue%s->addFallbackCatalogue($catalogue%s);

                    EOF
                ,
                $fallbackSuffix,
                $fallback,
                var_export($fallbackCatalogue->all(), true),
                $currentSuffix,
                $fallbackSuffix
            );
            $current = $fallbackCatalogue->getLocale();
            $fallbackCatalogue = $fallbackCatalogue->getFallbackCatalogue();
        }

        return $fallbackContent;
    }

    private function getCatalogueCachePath($locale)
    {
        return $this->cacheDir.'/catalogue.'.$locale.'.'.sha1(serialize($this->fallbackLocales)).'.php';
    }

    private function doLoadCatalogue($locale): void
    {
        $this->catalogues[$locale] = new MessageCatalogue($locale);

        if (isset($this->resources[$locale])) {
            foreach ($this->resources[$locale] as $resource) {
                if (!isset($this->loaders[$resource[0]])) {
                    throw new \RuntimeException(sprintf('The "%s" translation loader is not registered.', $resource[0]));
                }
                $this->catalogues[$locale]->addCatalogue($this->loaders[$resource[0]]->load($resource[1], $locale, $resource[2]));
            }
        }
    }

    private function loadFallbackCatalogues($locale): void
    {
        $current = $this->catalogues[$locale];

        foreach ($this->computeFallbackLocales($locale) as $fallback) {
            if (!isset($this->catalogues[$fallback])) {
                $this->initializeCatalogue($fallback);
            }

            $fallbackCatalogue = new MessageCatalogue($fallback, $this->catalogues[$fallback]->all());
            foreach ($this->catalogues[$fallback]->getResources() as $resource) {
                $fallbackCatalogue->addResource($resource);
            }
            $current->addFallbackCatalogue($fallbackCatalogue);
            $current = $fallbackCatalogue;
        }
    }

    /**
     * Provides the ConfigCache factory implementation, falling back to a
     * default implementation if necessary.
     *
     * @return ConfigCacheFactoryInterface $configCacheFactory
     */
    private function getConfigCacheFactory()
    {
        if (!$this->configCacheFactory) {
            $this->configCacheFactory = new ConfigCacheFactory($this->debug);
        }

        return $this->configCacheFactory;
    }
}
