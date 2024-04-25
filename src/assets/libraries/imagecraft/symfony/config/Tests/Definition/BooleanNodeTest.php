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
use Symfony\Component\Config\Definition\BooleanNode;
use Symfony\Component\Config\Definition\Exception\InvalidTypeException;

/**
 * @internal
 *
 * @coversNothing
 */
final class BooleanNodeTest extends TestCase
{
    /**
     * @dataProvider getValidValues
     *
     * @param mixed $value
     */
    public function testNormalize($value): void
    {
        $node = new BooleanNode('test');
        self::assertSame($value, $node->normalize($value));
    }

    /**
     * @dataProvider getValidValues
     *
     * @param bool $value
     */
    public function testValidNonEmptyValues($value): void
    {
        $node = new BooleanNode('test');
        $node->setAllowEmptyValue(false);

        self::assertSame($value, $node->finalize($value));
    }

    public static function getValidValues(): iterable
    {
        return [
            [false],
            [true],
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

        $node = new BooleanNode('test');
        $node->normalize($value);
    }

    public static function provideNormalizeThrowsExceptionOnInvalidValuesCases(): iterable
    {
        return [
            [null],
            [''],
            ['foo'],
            [0],
            [1],
            [0.0],
            [0.1],
            [[]],
            [['foo' => 'bar']],
            [new \stdClass()],
        ];
    }
}
