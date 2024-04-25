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
use Symfony\Component\Translation\Exception\NotFoundResourceException;
use Symfony\Component\Translation\Loader\ArrayLoader;
use Symfony\Component\Translation\Loader\YamlFileLoader;
use Symfony\Component\Translation\MessageCatalogue;
use Symfony\Component\Translation\MessageSelector;
use Symfony\Component\Translation\Translator;

/**
 * @internal
 *
 * @coversNothing
 */
final class TranslatorTest extends TestCase
{
    /**
     * @dataProvider      getInvalidLocalesTests
     *
     * @param mixed $locale
     */
    public function testConstructorInvalidLocale($locale): void
    {
        $this->expectException(\InvalidArgumentException::class);

        new Translator($locale, new MessageSelector());
    }

    /**
     * @dataProvider getValidLocalesTests
     *
     * @param mixed $locale
     */
    public function testConstructorValidLocale($locale): void
    {
        $translator = new Translator($locale, new MessageSelector());

        self::assertSame($locale, $translator->getLocale());
    }

    public function testConstructorWithoutLocale(): void
    {
        $translator = new Translator(null, new MessageSelector());

        self::assertNull($translator->getLocale());
    }

    public function testSetGetLocale(): void
    {
        $translator = new Translator('en');

        self::assertSame('en', $translator->getLocale());

        $translator->setLocale('fr');
        self::assertSame('fr', $translator->getLocale());
    }

    /**
     * @dataProvider      getInvalidLocalesTests
     *
     * @param mixed $locale
     */
    public function testSetInvalidLocale($locale): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $translator = new Translator('fr', new MessageSelector());
        $translator->setLocale($locale);
    }

    /**
     * @dataProvider getValidLocalesTests
     *
     * @param mixed $locale
     */
    public function testSetValidLocale($locale): void
    {
        $translator = new Translator($locale, new MessageSelector());
        $translator->setLocale($locale);

        self::assertSame($locale, $translator->getLocale());
    }

    public function testGetCatalogue(): void
    {
        $translator = new Translator('en');

        self::assertSame(new MessageCatalogue('en'), $translator->getCatalogue());

        $translator->setLocale('fr');
        self::assertSame(new MessageCatalogue('fr'), $translator->getCatalogue('fr'));
    }

    public function testGetCatalogueReturnsConsolidatedCatalogue(): void
    {
        /*
         * This will be useful once we refactor so that different domains will be loaded lazily (on-demand).
         * In that case, getCatalogue() will probably have to load all missing domains in order to return
         * one complete catalogue.
         */

        $locale = 'whatever';
        $translator = new Translator($locale);
        $translator->addLoader('loader-a', new ArrayLoader());
        $translator->addLoader('loader-b', new ArrayLoader());
        $translator->addResource('loader-a', ['foo' => 'foofoo'], $locale, 'domain-a');
        $translator->addResource('loader-b', ['bar' => 'foobar'], $locale, 'domain-b');

        /*
         * Test that we get a single catalogue comprising messages
         * from different loaders and different domains
         */
        $catalogue = $translator->getCatalogue($locale);
        self::assertTrue($catalogue->defines('foo', 'domain-a'));
        self::assertTrue($catalogue->defines('bar', 'domain-b'));
    }

    public function testSetFallbackLocales(): void
    {
        $translator = new Translator('en');
        $translator->addLoader('array', new ArrayLoader());
        $translator->addResource('array', ['foo' => 'foofoo'], 'en');
        $translator->addResource('array', ['bar' => 'foobar'], 'fr');

        // force catalogue loading
        $translator->trans('bar');

        $translator->setFallbackLocales(['fr']);
        self::assertSame('foobar', $translator->trans('bar'));
    }

    public function testSetFallbackLocalesMultiple(): void
    {
        $translator = new Translator('en');
        $translator->addLoader('array', new ArrayLoader());
        $translator->addResource('array', ['foo' => 'foo (en)'], 'en');
        $translator->addResource('array', ['bar' => 'bar (fr)'], 'fr');

        // force catalogue loading
        $translator->trans('bar');

        $translator->setFallbackLocales(['fr_FR', 'fr']);
        self::assertSame('bar (fr)', $translator->trans('bar'));
    }

    /**
     * @dataProvider      getInvalidLocalesTests
     *
     * @param mixed $locale
     */
    public function testSetFallbackInvalidLocales($locale): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $translator = new Translator('fr', new MessageSelector());
        $translator->setFallbackLocales(['fr', $locale]);
    }

    /**
     * @dataProvider getValidLocalesTests
     *
     * @param mixed $locale
     */
    public function testSetFallbackValidLocales($locale): void
    {
        $translator = new Translator($locale, new MessageSelector());
        $translator->setFallbackLocales(['fr', $locale]);
        // no assertion. this method just asserts that no exception is thrown
        $this->addToAssertionCount(1);
    }

    public function testTransWithFallbackLocale(): void
    {
        $translator = new Translator('fr_FR');
        $translator->setFallbackLocales(['en']);

        $translator->addLoader('array', new ArrayLoader());
        $translator->addResource('array', ['bar' => 'foobar'], 'en');

        self::assertSame('foobar', $translator->trans('bar'));
    }

    /**
     * @dataProvider      getInvalidLocalesTests
     *
     * @param mixed $locale
     */
    public function testAddResourceInvalidLocales($locale): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $translator = new Translator('fr', new MessageSelector());
        $translator->addResource('array', ['foo' => 'foofoo'], $locale);
    }

    /**
     * @dataProvider getValidLocalesTests
     *
     * @param mixed $locale
     */
    public function testAddResourceValidLocales($locale): void
    {
        $translator = new Translator('fr', new MessageSelector());
        $translator->addResource('array', ['foo' => 'foofoo'], $locale);
        // no assertion. this method just asserts that no exception is thrown
        $this->addToAssertionCount(1);
    }

    public function testAddResourceAfterTrans(): void
    {
        $translator = new Translator('fr');
        $translator->addLoader('array', new ArrayLoader());

        $translator->setFallbackLocales(['en']);

        $translator->addResource('array', ['foo' => 'foofoo'], 'en');
        self::assertSame('foofoo', $translator->trans('foo'));

        $translator->addResource('array', ['bar' => 'foobar'], 'en');
        self::assertSame('foobar', $translator->trans('bar'));
    }

    /**
     * @dataProvider      getTransFileTests
     *
     * @param mixed $format
     * @param mixed $loader
     */
    public function testTransWithoutFallbackLocaleFile($format, $loader): void
    {
        $this->expectException(NotFoundResourceException::class);

        $loaderClass = 'Symfony\\Component\\Translation\\Loader\\'.$loader;
        $translator = new Translator('en');
        $translator->addLoader($format, new $loaderClass());
        $translator->addResource($format, __DIR__.'/fixtures/non-existing', 'en');
        $translator->addResource($format, __DIR__.'/fixtures/resources.'.$format, 'en');

        // force catalogue loading
        $translator->trans('foo');
    }

    /**
     * @dataProvider getTransFileTests
     *
     * @param mixed $format
     * @param mixed $loader
     */
    public function testTransWithFallbackLocaleFile($format, $loader): void
    {
        $loaderClass = 'Symfony\\Component\\Translation\\Loader\\'.$loader;
        $translator = new Translator('en_GB');
        $translator->addLoader($format, new $loaderClass());
        $translator->addResource($format, __DIR__.'/fixtures/non-existing', 'en_GB');
        $translator->addResource($format, __DIR__.'/fixtures/resources.'.$format, 'en', 'resources');

        self::assertSame('bar', $translator->trans('foo', [], 'resources'));
    }

    public function testTransWithFallbackLocaleBis(): void
    {
        $translator = new Translator('en_US');
        $translator->addLoader('array', new ArrayLoader());
        $translator->addResource('array', ['foo' => 'foofoo'], 'en_US');
        $translator->addResource('array', ['bar' => 'foobar'], 'en');
        self::assertSame('foobar', $translator->trans('bar'));
    }

    public function testTransWithFallbackLocaleTer(): void
    {
        $translator = new Translator('fr_FR');
        $translator->addLoader('array', new ArrayLoader());
        $translator->addResource('array', ['foo' => 'foo (en_US)'], 'en_US');
        $translator->addResource('array', ['bar' => 'bar (en)'], 'en');

        $translator->setFallbackLocales(['en_US', 'en']);

        self::assertSame('foo (en_US)', $translator->trans('foo'));
        self::assertSame('bar (en)', $translator->trans('bar'));
    }

    public function testTransNonExistentWithFallback(): void
    {
        $translator = new Translator('fr');
        $translator->setFallbackLocales(['en']);
        $translator->addLoader('array', new ArrayLoader());
        self::assertSame('non-existent', $translator->trans('non-existent'));
    }

    public function testWhenAResourceHasNoRegisteredLoader(): void
    {
        $this->expectException(\RuntimeException::class);

        $translator = new Translator('en');
        $translator->addResource('array', ['foo' => 'foofoo'], 'en');

        $translator->trans('foo');
    }

    public function testNestedFallbackCatalogueWhenUsingMultipleLocales(): void
    {
        $translator = new Translator('fr');
        $translator->setFallbackLocales(['ru', 'en']);

        $translator->getCatalogue('fr');

        self::assertNotNull($translator->getCatalogue('ru')->getFallbackCatalogue());
    }

    public function testFallbackCatalogueResources(): void
    {
        $translator = new Translator('en_GB', new MessageSelector());
        $translator->addLoader('yml', new YamlFileLoader());
        $translator->addResource('yml', __DIR__.'/fixtures/empty.yml', 'en_GB');
        $translator->addResource('yml', __DIR__.'/fixtures/resources.yml', 'en');

        // force catalogue loading
        self::assertSame('bar', $translator->trans('foo', []));

        $resources = $translator->getCatalogue('en')->getResources();
        self::assertCount(1, $resources);
        self::assertContains(__DIR__.\DIRECTORY_SEPARATOR.'fixtures'.\DIRECTORY_SEPARATOR.'resources.yml', $resources);

        $resources = $translator->getCatalogue('en_GB')->getResources();
        self::assertCount(2, $resources);
        self::assertContains(__DIR__.\DIRECTORY_SEPARATOR.'fixtures'.\DIRECTORY_SEPARATOR.'empty.yml', $resources);
        self::assertContains(__DIR__.\DIRECTORY_SEPARATOR.'fixtures'.\DIRECTORY_SEPARATOR.'resources.yml', $resources);
    }

    /**
     * @dataProvider provideTransCases
     *
     * @param mixed $expected
     * @param mixed $id
     * @param mixed $translation
     * @param mixed $parameters
     * @param mixed $locale
     * @param mixed $domain
     */
    public function testTrans($expected, $id, $translation, $parameters, $locale, $domain): void
    {
        $translator = new Translator('en');
        $translator->addLoader('array', new ArrayLoader());
        $translator->addResource('array', [(string) $id => $translation], $locale, $domain);

        self::assertSame($expected, $translator->trans($id, $parameters, $domain, $locale));
    }

    /**
     * @dataProvider      getInvalidLocalesTests
     *
     * @param mixed $locale
     */
    public function testTransInvalidLocale($locale): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $translator = new Translator('en', new MessageSelector());
        $translator->addLoader('array', new ArrayLoader());
        $translator->addResource('array', ['foo' => 'foofoo'], 'en');

        $translator->trans('foo', [], '', $locale);
    }

    /**
     * @dataProvider      getValidLocalesTests
     *
     * @param mixed $locale
     */
    public function testTransValidLocale($locale): void
    {
        $translator = new Translator($locale, new MessageSelector());
        $translator->addLoader('array', new ArrayLoader());
        $translator->addResource('array', ['test' => 'OK'], $locale);

        self::assertSame('OK', $translator->trans('test'));
        self::assertSame('OK', $translator->trans('test', [], null, $locale));
    }

    /**
     * @dataProvider provideFlattenedTransCases
     *
     * @param mixed $expected
     * @param mixed $messages
     * @param mixed $id
     */
    public function testFlattenedTrans($expected, $messages, $id): void
    {
        $translator = new Translator('en');
        $translator->addLoader('array', new ArrayLoader());
        $translator->addResource('array', $messages, 'fr', '');

        self::assertSame($expected, $translator->trans($id, [], '', 'fr'));
    }

    /**
     * @dataProvider provideTransChoiceCases
     *
     * @param mixed $expected
     * @param mixed $id
     * @param mixed $translation
     * @param mixed $number
     * @param mixed $parameters
     * @param mixed $locale
     * @param mixed $domain
     */
    public function testTransChoice($expected, $id, $translation, $number, $parameters, $locale, $domain): void
    {
        $translator = new Translator('en');
        $translator->addLoader('array', new ArrayLoader());
        $translator->addResource('array', [(string) $id => $translation], $locale, $domain);

        self::assertSame($expected, $translator->transChoice($id, $number, $parameters, $domain, $locale));
    }

    /**
     * @dataProvider      getInvalidLocalesTests
     *
     * @param mixed $locale
     */
    public function testTransChoiceInvalidLocale($locale): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $translator = new Translator('en', new MessageSelector());
        $translator->addLoader('array', new ArrayLoader());
        $translator->addResource('array', ['foo' => 'foofoo'], 'en');

        $translator->transChoice('foo', 1, [], '', $locale);
    }

    /**
     * @dataProvider      getValidLocalesTests
     *
     * @param mixed $locale
     */
    public function testTransChoiceValidLocale($locale): void
    {
        $translator = new Translator('en', new MessageSelector());
        $translator->addLoader('array', new ArrayLoader());
        $translator->addResource('array', ['foo' => 'foofoo'], 'en');

        $translator->transChoice('foo', 1, [], '', $locale);
        // no assertion. this method just asserts that no exception is thrown
        $this->addToAssertionCount(1);
    }

    public static function getTransFileTests(): iterable
    {
        return [
            ['csv', 'CsvFileLoader'],
            ['ini', 'IniFileLoader'],
            ['mo', 'MoFileLoader'],
            ['po', 'PoFileLoader'],
            ['php', 'PhpFileLoader'],
            ['ts', 'QtFileLoader'],
            ['xlf', 'XliffFileLoader'],
            ['yml', 'YamlFileLoader'],
            ['json', 'JsonFileLoader'],
        ];
    }

    public static function provideTransCases(): iterable
    {
        return [
            ['Symfony est super !', 'Symfony is great!', 'Symfony est super !', [], 'fr', ''],
            ['Symfony est awesome !', 'Symfony is %what%!', 'Symfony est %what% !', ['%what%' => 'awesome'], 'fr', ''],
            ['Symfony est super !', new StringClass('Symfony is great!'), 'Symfony est super !', [], 'fr', ''],
        ];
    }

    public static function provideFlattenedTransCases(): iterable
    {
        $messages = [
            'symfony' => [
                'is' => [
                    'great' => 'Symfony est super!',
                ],
            ],
            'foo' => [
                'bar' => [
                    'baz' => 'Foo Bar Baz',
                ],
                'baz' => 'Foo Baz',
            ],
        ];

        return [
            ['Symfony est super!', $messages, 'symfony.is.great'],
            ['Foo Bar Baz', $messages, 'foo.bar.baz'],
            ['Foo Baz', $messages, 'foo.baz'],
        ];
    }

    public static function provideTransChoiceCases(): iterable
    {
        return [
            ['Il y a 0 pomme', '{0} There are no appless|{1} There is one apple|]1,Inf] There is %count% apples', '[0,1] Il y a %count% pomme|]1,Inf] Il y a %count% pommes', 0, ['%count%' => 0], 'fr', ''],
            ['Il y a 1 pomme', '{0} There are no appless|{1} There is one apple|]1,Inf] There is %count% apples', '[0,1] Il y a %count% pomme|]1,Inf] Il y a %count% pommes', 1, ['%count%' => 1], 'fr', ''],
            ['Il y a 10 pommes', '{0} There are no appless|{1} There is one apple|]1,Inf] There is %count% apples', '[0,1] Il y a %count% pomme|]1,Inf] Il y a %count% pommes', 10, ['%count%' => 10], 'fr', ''],

            ['Il y a 0 pomme', 'There is one apple|There is %count% apples', 'Il y a %count% pomme|Il y a %count% pommes', 0, ['%count%' => 0], 'fr', ''],
            ['Il y a 1 pomme', 'There is one apple|There is %count% apples', 'Il y a %count% pomme|Il y a %count% pommes', 1, ['%count%' => 1], 'fr', ''],
            ['Il y a 10 pommes', 'There is one apple|There is %count% apples', 'Il y a %count% pomme|Il y a %count% pommes', 10, ['%count%' => 10], 'fr', ''],

            ['Il y a 0 pomme', 'one: There is one apple|more: There is %count% apples', 'one: Il y a %count% pomme|more: Il y a %count% pommes', 0, ['%count%' => 0], 'fr', ''],
            ['Il y a 1 pomme', 'one: There is one apple|more: There is %count% apples', 'one: Il y a %count% pomme|more: Il y a %count% pommes', 1, ['%count%' => 1], 'fr', ''],
            ['Il y a 10 pommes', 'one: There is one apple|more: There is %count% apples', 'one: Il y a %count% pomme|more: Il y a %count% pommes', 10, ['%count%' => 10], 'fr', ''],

            ['Il n\'y a aucune pomme', '{0} There are no apples|one: There is one apple|more: There is %count% apples', '{0} Il n\'y a aucune pomme|one: Il y a %count% pomme|more: Il y a %count% pommes', 0, ['%count%' => 0], 'fr', ''],
            ['Il y a 1 pomme', '{0} There are no apples|one: There is one apple|more: There is %count% apples', '{0} Il n\'y a aucune pomme|one: Il y a %count% pomme|more: Il y a %count% pommes', 1, ['%count%' => 1], 'fr', ''],
            ['Il y a 10 pommes', '{0} There are no apples|one: There is one apple|more: There is %count% apples', '{0} Il n\'y a aucune pomme|one: Il y a %count% pomme|more: Il y a %count% pommes', 10, ['%count%' => 10], 'fr', ''],

            ['Il y a 0 pomme', new StringClass('{0} There are no appless|{1} There is one apple|]1,Inf] There is %count% apples'), '[0,1] Il y a %count% pomme|]1,Inf] Il y a %count% pommes', 0, ['%count%' => 0], 'fr', ''],
        ];
    }

    public static function getInvalidLocalesTests(): iterable
    {
        return [
            ['fr FR'],
            ['français'],
            ['fr+en'],
            ['utf#8'],
            ['fr&en'],
            ['fr~FR'],
            [' fr'],
            ['fr '],
            ['fr*'],
            ['fr/FR'],
            ['fr\\FR'],
        ];
    }

    public static function getValidLocalesTests(): iterable
    {
        return [
            [''],
            [null],
            ['fr'],
            ['francais'],
            ['FR'],
            ['frFR'],
            ['fr-FR'],
            ['fr_FR'],
            ['fr.FR'],
            ['fr-FR.UTF8'],
            ['sr@latin'],
        ];
    }

    public function testTransChoiceFallback(): void
    {
        $translator = new Translator('ru');
        $translator->setFallbackLocales(['en']);
        $translator->addLoader('array', new ArrayLoader());
        $translator->addResource('array', ['some_message2' => 'one thing|%count% things'], 'en');

        self::assertSame('10 things', $translator->transChoice('some_message2', 10, ['%count%' => 10]));
    }

    public function testTransChoiceFallbackBis(): void
    {
        $translator = new Translator('ru');
        $translator->setFallbackLocales(['en_US', 'en']);
        $translator->addLoader('array', new ArrayLoader());
        $translator->addResource('array', ['some_message2' => 'one thing|%count% things'], 'en_US');

        self::assertSame('10 things', $translator->transChoice('some_message2', 10, ['%count%' => 10]));
    }

    public function testTransChoiceFallbackWithNoTranslation(): void
    {
        $translator = new Translator('ru');
        $translator->setFallbackLocales(['en']);
        $translator->addLoader('array', new ArrayLoader());

        // consistent behavior with Translator::trans(), which returns the string
        // unchanged if it can't be found
        self::assertSame('some_message2', $translator->transChoice('some_message2', 10, ['%count%' => 10]));
    }

    /**
     * @group legacy
     *
     * @dataProvider provideLegacyGetMessagesCases
     *
     * @param mixed $resources
     * @param mixed $locale
     * @param mixed $expected
     */
    public function testLegacyGetMessages($resources, $locale, $expected): void
    {
        $locales = array_keys($resources);
        $_locale = null !== $locale ? $locale : reset($locales);
        $locales = \array_slice($locales, 0, array_search($_locale, $locales, true));

        $translator = new Translator($_locale, new MessageSelector());
        $translator->setFallbackLocales(array_reverse($locales));
        $translator->addLoader('array', new ArrayLoader());
        foreach ($resources as $_locale => $domainMessages) {
            foreach ($domainMessages as $domain => $messages) {
                $translator->addResource('array', $messages, $_locale, $domain);
            }
        }
        $result = $translator->getMessages($locale);

        self::assertSame($expected, $result);
    }

    public static function provideLegacyGetMessagesCases(): iterable
    {
        $resources = [
            'en' => [
                'jsmessages' => [
                    'foo' => 'foo (EN)',
                    'bar' => 'bar (EN)',
                ],
                'messages' => [
                    'foo' => 'foo messages (EN)',
                ],
                'validators' => [
                    'int' => 'integer (EN)',
                ],
            ],
            'pt-PT' => [
                'messages' => [
                    'foo' => 'foo messages (PT)',
                ],
                'validators' => [
                    'str' => 'integer (PT)',
                ],
            ],
            'pt_BR' => [
                'validators' => [
                    'int' => 'integer (BR)',
                ],
            ],
        ];

        return [
            [$resources, null,
                [
                    'jsmessages' => [
                        'foo' => 'foo (EN)',
                        'bar' => 'bar (EN)',
                    ],
                    'messages' => [
                        'foo' => 'foo messages (EN)',
                    ],
                    'validators' => [
                        'int' => 'integer (EN)',
                    ],
                ],
            ],
            [$resources, 'en',
                [
                    'jsmessages' => [
                        'foo' => 'foo (EN)',
                        'bar' => 'bar (EN)',
                    ],
                    'messages' => [
                        'foo' => 'foo messages (EN)',
                    ],
                    'validators' => [
                        'int' => 'integer (EN)',
                    ],
                ],
            ],
            [$resources, 'pt-PT',
                [
                    'jsmessages' => [
                        'foo' => 'foo (EN)',
                        'bar' => 'bar (EN)',
                    ],
                    'messages' => [
                        'foo' => 'foo messages (PT)',
                    ],
                    'validators' => [
                        'int' => 'integer (EN)',
                        'str' => 'integer (PT)',
                    ],
                ],
            ],
            [$resources, 'pt_BR',
                [
                    'jsmessages' => [
                        'foo' => 'foo (EN)',
                        'bar' => 'bar (EN)',
                    ],
                    'messages' => [
                        'foo' => 'foo messages (PT)',
                    ],
                    'validators' => [
                        'int' => 'integer (BR)',
                        'str' => 'integer (PT)',
                    ],
                ],
            ],
        ];
    }
}

class StringClass
{
    protected $str;

    public function __construct($str)
    {
        $this->str = $str;
    }

    public function __toString()
    {
        return $this->str;
    }
}
