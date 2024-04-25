<?php



/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Config\Tests\Definition;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\Config\Definition\Exception\InvalidTypeException;
use Symfony\Component\Config\Definition\ScalarNode;

/**
 * @internal
 *
 * @coversNothing
 */
final class ScalarNodeTest extends TestCase
{
    /**
     * @dataProvider provideNormalizeCases
     *
     * @param mixed $value
     */
    public function testNormalize($value): void
    {
        $node = new ScalarNode('test');
        self::assertSame($value, $node->normalize($value));
    }

    public static function provideNormalizeCases(): iterable
    {
        return [
            [false],
            [true],
            [null],
            [''],
            ['foo'],
            [0],
            [1],
            [0.0],
            [0.1],
        ];
    }

    /**
     * @dataProvider provideNormalizeThrowsExceptionOnInvalidValuesCases
     *
     * @param mixed $value
     */
    public function testNormalizeThrowsExceptionOnInvalidValues($value): void
    {
        $this->expectException(InvalidTypeException::class);

        $node = new ScalarNode('test');
        $node->normalize($value);
    }

    public static function provideNormalizeThrowsExceptionOnInvalidValuesCases(): iterable
    {
        return [
            [[]],
            [['foo' => 'bar']],
            [new \stdClass()],
        ];
    }

    public function testNormalizeThrowsExceptionWithoutHint(): void
    {
        $node = new ScalarNode('test');

        if (method_exists($this, 'expectException')) {
            $this->expectException('Symfony\Component\Config\Definition\Exception\InvalidTypeException');
            $this->expectExceptionMessage('Invalid type for path "test". Expected scalar, but got array.');
        } else {
            $this->expectException('Symfony\Component\Config\Definition\Exception\InvalidTypeException');
            $this->expectExceptionMessage('Invalid type for path "test". Expected scalar, but got array.');
        }

        $node->normalize([]);
    }

    public function testNormalizeThrowsExceptionWithErrorMessage(): void
    {
        $node = new ScalarNode('test');
        $node->setInfo('"the test value"');

        if (method_exists($this, 'expectException')) {
            $this->expectException('Symfony\Component\Config\Definition\Exception\InvalidTypeException');
            $this->expectExceptionMessage("Invalid type for path \"test\". Expected scalar, but got array.\nHint: \"the test value\"");
        } else {
            $this->expectException('Symfony\Component\Config\Definition\Exception\InvalidTypeException');
            $this->expectExceptionMessage("Invalid type for path \"test\". Expected scalar, but got array.\nHint: \"the test value\"");
        }

        $node->normalize([]);
    }

    /**
     * @dataProvider provideValidNonEmptyValuesCases
     *
     * @param mixed $value
     */
    public function testValidNonEmptyValues($value): void
    {
        $node = new ScalarNode('test');
        $node->setAllowEmptyValue(false);

        self::assertSame($value, $node->finalize($value));
    }

    public static function provideValidNonEmptyValuesCases(): iterable
    {
        return [
            [false],
            [true],
            ['foo'],
            [0],
            [1],
            [0.0],
            [0.1],
        ];
    }

    /**
     * @dataProvider provideNotAllowedEmptyValuesThrowExceptionCases
     *
     * @param mixed $value
     */
    public function testNotAllowedEmptyValuesThrowException($value): void
    {
        $this->expectException(InvalidConfigurationException::class);

        $node = new ScalarNode('test');
        $node->setAllowEmptyValue(false);
        $node->finalize($value);
    }

    public static function provideNotAllowedEmptyValuesThrowExceptionCases(): iterable
    {
        return [
            [null],
            [''],
        ];
    }
}
