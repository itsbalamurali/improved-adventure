<?php



/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Translation\Tests;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Intl\Util\IntlTestHelper;
use Symfony\Component\Translation\IdentityTranslator;

/**
 * @internal
 *
 * @coversNothing
 */
final class IdentityTranslatorTest extends TestCase
{
    /**
     * @dataProvider provideTransCases
     *
     * @param mixed $expected
     * @param mixed $id
     * @param mixed $parameters
     */
    public function testTrans($expected, $id, $parameters): void
    {
        $translator = new IdentityTranslator();

        self::assertSame($expected, $translator->trans($id, $parameters));
    }

    /**
     * @dataProvider getTransChoiceTests
     *
     * @param mixed $expected
     * @param mixed $id
     * @param mixed $number
     * @param mixed $parameters
     */
    public function testTransChoiceWithExplicitLocale($expected, $id, $number, $parameters): void
    {
        $translator = new IdentityTranslator();
        $translator->setLocale('en');

        self::assertSame($expected, $translator->transChoice($id, $number, $parameters));
    }

    /**
     * @dataProvider getTransChoiceTests
     *
     * @param mixed $expected
     * @param mixed $id
     * @param mixed $number
     * @param mixed $parameters
     */
    public function testTransChoiceWithDefaultLocale($expected, $id, $number, $parameters): void
    {
        \Locale::setDefault('en');

        $translator = new IdentityTranslator();

        self::assertSame($expected, $translator->transChoice($id, $number, $parameters));
    }

    public function testGetSetLocale(): void
    {
        $translator = new IdentityTranslator();
        $translator->setLocale('en');

        self::assertSame('en', $translator->getLocale());
    }

    public function testGetLocaleReturnsDefaultLocaleIfNotSet(): void
    {
        // in order to test with "pt_BR"
        IntlTestHelper::requireFullIntl($this, false);

        $translator = new IdentityTranslator();

        \Locale::setDefault('en');
        self::assertSame('en', $translator->getLocale());

        \Locale::setDefault('pt_BR');
        self::assertSame('pt_BR', $translator->getLocale());
    }

    public static function provideTransCases(): iterable
    {
        return [
            ['Symfony is great!', 'Symfony is great!', []],
            ['Symfony is awesome!', 'Symfony is %what%!', ['%what%' => 'awesome']],
        ];
    }

    public static function getTransChoiceTests(): iterable
    {
        return [
            ['There are no apples', '{0} There are no apples|{1} There is one apple|]1,Inf] There are %count% apples', 0, ['%count%' => 0]],
            ['There is one apple', '{0} There are no apples|{1} There is one apple|]1,Inf] There are %count% apples', 1, ['%count%' => 1]],
            ['There are 10 apples', '{0} There are no apples|{1} There is one apple|]1,Inf] There are %count% apples', 10, ['%count%' => 10]],
            ['There are 0 apples', 'There is 1 apple|There are %count% apples', 0, ['%count%' => 0]],
            ['There is 1 apple', 'There is 1 apple|There are %count% apples', 1, ['%count%' => 1]],
            ['There are 10 apples', 'There is 1 apple|There are %count% apples', 10, ['%count%' => 10]],
            // custom validation messages may be coded with a fixed value
            ['There are 2 apples', 'There are 2 apples', 2, ['%count%' => 2]],
        ];
    }
}
