<?php



/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Config\Tests\Util;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\Util\XmlUtils;

/**
 * @internal
 *
 * @coversNothing
 */
final class XmlUtilsTest extends TestCase
{
    public function testLoadFile(): void
    {
        $fixtures = __DIR__.'/../Fixtures/Util/';

        try {
            XmlUtils::loadFile($fixtures.'invalid.xml');
            self::fail();
        } catch (\InvalidArgumentException $e) {
            self::assertContains('ERROR 77', $e->getMessage());
        }

        try {
            XmlUtils::loadFile($fixtures.'document_type.xml');
            self::fail();
        } catch (\InvalidArgumentException $e) {
            self::assertContains('Document types are not allowed', $e->getMessage());
        }

        try {
            XmlUtils::loadFile($fixtures.'invalid_schema.xml', $fixtures.'schema.xsd');
            self::fail();
        } catch (\InvalidArgumentException $e) {
            self::assertContains('ERROR 1845', $e->getMessage());
        }

        try {
            XmlUtils::loadFile($fixtures.'invalid_schema.xml', 'invalid_callback_or_file');
            self::fail();
        } catch (\InvalidArgumentException $e) {
            self::assertContains('XSD file or callable', $e->getMessage());
        }

        $mock = $this->getMockBuilder(__NAMESPACE__.'\Validator')->getMock();
        $mock->expects(self::exactly(2))->method('validate')->will(self::onConsecutiveCalls(false, true));

        try {
            XmlUtils::loadFile($fixtures.'valid.xml', [$mock, 'validate']);
            self::fail();
        } catch (\InvalidArgumentException $e) {
            self::assertContains('is not valid', $e->getMessage());
        }

        self::assertInstanceOf('DOMDocument', XmlUtils::loadFile($fixtures.'valid.xml', [$mock, 'validate']));
        self::assertSame([], libxml_get_errors());
    }

    public function testLoadFileWithInternalErrorsEnabled(): void
    {
        $internalErrors = libxml_use_internal_errors(true);

        self::assertSame([], libxml_get_errors());
        self::assertInstanceOf('DOMDocument', XmlUtils::loadFile(__DIR__.'/../Fixtures/Util/invalid_schema.xml'));
        self::assertSame([], libxml_get_errors());

        libxml_clear_errors();
        libxml_use_internal_errors($internalErrors);
    }

    /**
     * @dataProvider provideConvertDomToArrayCases
     *
     * @param mixed $expected
     * @param mixed $xml
     * @param mixed $root
     * @param mixed $checkPrefix
     */
    public function testConvertDomToArray($expected, $xml, $root = false, $checkPrefix = true): void
    {
        $dom = new \DOMDocument();
        $dom->loadXML($root ? $xml : '<root>'.$xml.'</root>');

        self::assertSame($expected, XmlUtils::convertDomElementToArray($dom->documentElement, $checkPrefix));
    }

    public static function provideConvertDomToArrayCases(): iterable
    {
        return [
            [null, ''],
            ['bar', 'bar'],
            [['bar' => 'foobar'], '<foo bar="foobar" />', true],
            [['foo' => null], '<foo />'],
            [['foo' => 'bar'], '<foo>bar</foo>'],
            [['foo' => ['foo' => 'bar']], '<foo foo="bar"/>'],
            [['foo' => ['foo' => 0]], '<foo><foo>0</foo></foo>'],
            [['foo' => ['foo' => 'bar']], '<foo><foo>bar</foo></foo>'],
            [['foo' => ['foo' => 'bar', 'value' => 'text']], '<foo foo="bar">text</foo>'],
            [['foo' => ['attr' => 'bar', 'foo' => 'text']], '<foo attr="bar"><foo>text</foo></foo>'],
            [['foo' => ['bar', 'text']], '<foo>bar</foo><foo>text</foo>'],
            [['foo' => [['foo' => 'bar'], ['foo' => 'text']]], '<foo foo="bar"/><foo foo="text" />'],
            [['foo' => ['foo' => ['bar', 'text']]], '<foo foo="bar"><foo>text</foo></foo>'],
            [['foo' => 'bar'], '<foo><!-- Comment -->bar</foo>'],
            [['foo' => 'text'], '<foo xmlns:h="http://www.example.org/bar" h:bar="bar">text</foo>'],
            [['foo' => ['bar' => 'bar', 'value' => 'text']], '<foo xmlns:h="http://www.example.org/bar" h:bar="bar">text</foo>', false, false],
            [['attr' => 1, 'b' => 'hello'], '<foo:a xmlns:foo="http://www.example.org/foo" xmlns:h="http://www.example.org/bar" attr="1" h:bar="bar"><foo:b>hello</foo:b><h:c>2</h:c></foo:a>', true],
        ];
    }

    /**
     * @dataProvider providePhpizeCases
     *
     * @param mixed $expected
     * @param mixed $value
     */
    public function testPhpize($expected, $value): void
    {
        self::assertSame($expected, XmlUtils::phpize($value));
    }

    public static function providePhpizeCases(): iterable
    {
        return [
            ['', ''],
            [null, 'null'],
            [true, 'true'],
            [false, 'false'],
            [null, 'Null'],
            [true, 'True'],
            [false, 'False'],
            [0, '0'],
            [1, '1'],
            [-1, '-1'],
            [0777, '0777'],
            [255, '0xFF'],
            [100.0, '1e2'],
            [-120.0, '-1.2E2'],
            [-10_100.1, '-10100.1'],
            ['-10,100.1', '-10,100.1'],
            ['1234 5678 9101 1121 3141', '1234 5678 9101 1121 3141'],
            ['1,2,3,4', '1,2,3,4'],
            ['11,22,33,44', '11,22,33,44'],
            ['11,222,333,4', '11,222,333,4'],
            ['1,222,333,444', '1,222,333,444'],
            ['11,222,333,444', '11,222,333,444'],
            ['111,222,333,444', '111,222,333,444'],
            ['1111,2222,3333,4444,5555', '1111,2222,3333,4444,5555'],
            ['foo', 'foo'],
            [6, '0b0110'],
        ];
    }

    public function testLoadEmptyXmlFile(): void
    {
        $file = __DIR__.'/../Fixtures/foo.xml';

        if (method_exists($this, 'expectException')) {
            $this->expectException('InvalidArgumentException');
            $this->expectExceptionMessage(sprintf('File %s does not contain valid XML, it is empty.', $file));
        } else {
            $this->expectException('InvalidArgumentException');
            $this->expectExceptionMessage(sprintf('File %s does not contain valid XML, it is empty.', $file));
        }

        XmlUtils::loadFile($file);
    }

    // test for issue https://github.com/symfony/symfony/issues/9731
    public function testLoadWrongEmptyXMLWithErrorHandler(): void
    {
        $originalDisableEntities = libxml_disable_entity_loader(false);
        $errorReporting = error_reporting(-1);

        set_error_handler(static function ($errno, $errstr): void {
            throw new \Exception($errstr, $errno);
        });

        $file = __DIR__.'/../Fixtures/foo.xml';

        try {
            try {
                XmlUtils::loadFile($file);
                self::fail('An exception should have been raised');
            } catch (\InvalidArgumentException $e) {
                self::assertSame(sprintf('File %s does not contain valid XML, it is empty.', $file), $e->getMessage());
            }
        } catch (\Exception $e) {
            restore_error_handler();
            error_reporting($errorReporting);

            throw $e;
        }

        restore_error_handler();
        error_reporting($errorReporting);

        $disableEntities = libxml_disable_entity_loader(true);
        libxml_disable_entity_loader($disableEntities);

        libxml_disable_entity_loader($originalDisableEntities);

        self::assertFalse($disableEntities);

        // should not throw an exception
        XmlUtils::loadFile(__DIR__.'/../Fixtures/Util/valid.xml', __DIR__.'/../Fixtures/Util/schema.xsd');
    }
}

interface Validator
{
    public function validate();
}
