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
 * IdentityTranslator does not translate anything.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class IdentityTranslator implements TranslatorInterface
{
    private $selector;
    private $locale;

    /**
     * @param null|MessageSelector $selector The message selector for pluralization
     */
    public function __construct(?MessageSelector $selector = null)
    {
        $this->selector = $selector ?: new MessageSelector();
    }

    public function setLocale($locale): void
    {
        $this->locale = $locale;
    }

    public function getLocale()
    {
        return $this->locale ?: \Locale::getDefault();
    }

    public function trans($id, array $parameters = [], $domain = null, $locale = null)
    {
        return strtr((string) $id, $parameters);
    }

    public function transChoice($id, $number, array $parameters = [], $domain = null, $locale = null)
    {
        return strtr($this->selector->choose((string) $id, (int) $number, $locale ?: $this->getLocale()), $parameters);
    }
}
