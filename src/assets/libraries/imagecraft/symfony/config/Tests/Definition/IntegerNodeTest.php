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
use Symfony\Component\Config\Definition\IntegerNode;

/**
 * @internal
 *
 * @coversNothing
 */
final class IntegerNodeTest extends TestCase
{
    /**
     * @dataProvider getValidValues
     *
     * @param mixed $value
     */
    public function testNormalize($value): void
    {
        $node = new IntegerNode('test');
        self::assertSame($value, $node->normalize($value));
    }

    /**
     * @dataProvider getValidValues
     *
     * @param int $value
     */
    public function testValidNonEmptyValues($value): void
    {
        $node = new IntegerNode('test');
        $node->setAllowEmptyValue(false);

        self::assertSame($value, $node->finalize($value));
    }

    public static function getValidValues(): iterable
    {
        return [
            [1_798],
            [-678],
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

        $node = new IntegerNode('test');
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
            [0.0],
            [0.1],
            [[]],
            [['foo' => 'bar']],
            [new \stdClass()],
        ];
    }
}
