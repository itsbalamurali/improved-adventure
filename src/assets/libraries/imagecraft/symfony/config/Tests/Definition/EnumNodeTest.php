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
use Symfony\Component\Config\Definition\EnumNode;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

/**
 * @internal
 *
 * @coversNothing
 */
final class EnumNodeTest extends TestCase
{
    public function testFinalizeValue(): void
    {
        $node = new EnumNode('foo', null, ['foo', 'bar']);
        self::assertSame('foo', $node->finalize('foo'));
    }

    public function testConstructionWithNoValues(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('$values must contain at least one element.');

        new EnumNode('foo', null, []);
    }

    public function testConstructionWithOneValue(): void
    {
        $node = new EnumNode('foo', null, ['foo']);
        self::assertSame('foo', $node->finalize('foo'));
    }

    public function testConstructionWithOneDistinctValue(): void
    {
        $node = new EnumNode('foo', null, ['foo', 'foo']);
        self::assertSame('foo', $node->finalize('foo'));
    }

    public function testFinalizeWithInvalidValue(): void
    {
        $this->expectException(InvalidConfigurationException::class);
        $this->expectExceptionMessage('The value "foobar" is not allowed for path "foo". Permissible values: "foo", "bar"');

        $node = new EnumNode('foo', null, ['foo', 'bar']);
        $node->finalize('foobar');
    }
}
