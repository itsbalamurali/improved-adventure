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

/**
 * @author Abdellatif Ait boudad <a.aitboudad@gmail.com>
 */
class DataCollectorTranslator implements TranslatorInterface, TranslatorBagInterface
{
    public const MESSAGE_DEFINED = 0;
    public const MESSAGE_MISSING = 1;
    public const MESSAGE_EQUALS_FALLBACK = 2;

    /**
     * @var TranslatorBagInterface|TranslatorInterface
     */
    private $translator;

    private $messages = [];

    /**
     * @param TranslatorInterface $translator The translator must implement TranslatorBagInterface
     */
    public function __construct(TranslatorInterface $translator)
    {
        if (!$translator instanceof TranslatorBagInterface) {
            throw new \InvalidArgumentException(sprintf('The Translator "%s" must implement TranslatorInterface and TranslatorBagInterface.', \get_class($translator)));
        }

        $this->translator = $translator;
    }

    /**
     * Passes through all unknown calls onto the translator object.
     *
     * @param mixed $method
     * @param mixed $args
     */
    public function __call($method, $args)
    {
        return \call_user_func_array([$this->translator, $method], $args);
    }

    public function trans($id, array $parameters = [], $domain = null, $locale = null)
    {
        $trans = $this->translator->trans($id, $parameters, $domain, $locale);
        $this->collectMessage($locale, $domain, $id, $trans, $parameters);

        return $trans;
    }

    public function transChoice($id, $number, array $parameters = [], $domain = null, $locale = null)
    {
        $trans = $this->translator->transChoice($id, $number, $parameters, $domain, $locale);
        $this->collectMessage($locale, $domain, $id, $trans, $parameters, $number);

        return $trans;
    }

    public function setLocale($locale): void
    {
        $this->translator->setLocale($locale);
    }

    public function getLocale()
    {
        return $this->translator->getLocale();
    }

    public function getCatalogue($locale = null)
    {
        return $this->translator->getCatalogue($locale);
    }

    /**
     * Gets the fallback locales.
     *
     * @return array The fallback locales
     */
    public function getFallbackLocales()
    {
        if ($this->translator instanceof Translator || method_exists($this->translator, 'getFallbackLocales')) {
            return $this->translator->getFallbackLocales();
        }

        return [];
    }

    /**
     * @return array
     */
    public function getCollectedMessages()
    {
        return $this->messages;
    }

    /**
     * @param null|string $locale
     * @param null|string $domain
     * @param string      $id
     * @param string      $translation
     * @param null|array  $parameters
     * @param null|int    $number
     */
    private function collectMessage($locale, $domain, $id, $translation, $parameters = [], $number = null): void
    {
        if (null === $domain) {
            $domain = 'messages';
        }

        $id = (string) $id;
        $catalogue = $this->translator->getCatalogue($locale);
        $locale = $catalogue->getLocale();
        if ($catalogue->defines($id, $domain)) {
            $state = self::MESSAGE_DEFINED;
        } elseif ($catalogue->has($id, $domain)) {
            $state = self::MESSAGE_EQUALS_FALLBACK;

            $fallbackCatalogue = $catalogue->getFallbackCatalogue();
            while ($fallbackCatalogue) {
                if ($fallbackCatalogue->defines($id, $domain)) {
                    $locale = $fallbackCatalogue->getLocale();

                    break;
                }

                $fallbackCatalogue = $fallbackCatalogue->getFallbackCatalogue();
            }
        } else {
            $state = self::MESSAGE_MISSING;
        }

        $this->messages[] = [
            'locale' => $locale,
            'domain' => $domain,
            'id' => $id,
            'translation' => $translation,
            'parameters' => $parameters,
            'transChoiceNumber' => $number,
            'state' => $state,
        ];
    }
}
