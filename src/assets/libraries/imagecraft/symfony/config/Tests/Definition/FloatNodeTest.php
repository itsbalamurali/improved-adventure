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
use Symfony\Component\Config\Definition\Exception\InvalidTypeException;
use Symfony\Component\Config\Definition\FloatNode;

/**
 * @internal
 *
 * @coversNothing
 */
final class FloatNodeTest extends TestCase
{
    /**
     * @dataProvider getValidValues
     *
     * @param mixed $value
     */
    public function testNormalize($value): void
    {
        $node = new FloatNode('test');
        self::assertSame($value, $node->normalize($value));
    }

    /**
     * @dataProvider getValidValues
     *
     * @param int $value
     */
    public function testValidNonEmptyValues($value): void
    {
        $node = new FloatNode('test');
        $node->setAllowEmptyValue(false);

        self::assertSame($value, $node->finalize($value));
    }

    public static function getValidValues(): iterable
    {
        return [
            [1_798.0],
            [-678.987],
            [12.56E45],
            [0.0],
            // Integer are accepted too, they will be cast
            [17],
            [-10],
            [0],
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

        $node = new FloatNode('test');
        $node->normalize($value);
    }

    public static function provideNormalizeThrowsExceptionOnInvalidValuesCases(): iterable
    {
        return [
            [null],
            [''],
            ['foo'],
            [true],
            [false],
            [[]],
            [['foo' => 'bar']],
            [new \stdClass()],
        ];
    }
}
