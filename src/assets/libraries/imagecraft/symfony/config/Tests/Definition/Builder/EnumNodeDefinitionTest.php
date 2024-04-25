<?php



/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Config\Tests\Definition\Builder;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\Definition\Builder\EnumNodeDefinition;

/**
 * @internal
 *
 * @coversNothing
 */
final class EnumNodeDefinitionTest extends TestCase
{
    public function testWithOneValue(): void
    {
        $def = new EnumNodeDefinition('foo');
        $def->values(['foo']);

        $node = $def->getNode();
        self::assertSame(['foo'], $node->getValues());
    }

    public function testWithOneDistinctValue(): void
    {
        $def = new EnumNodeDefinition('foo');
        $def->values(['foo', 'foo']);

        $node = $def->getNode();
        self::assertSame(['foo'], $node->getValues());
    }

    public function testNoValuesPassed(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('You must call ->values() on enum nodes.');

        $def = new EnumNodeDefinition('foo');
        $def->getNode();
    }

    public function testWithNoValues(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('->values() must be called with at least one value.');

        $def = new EnumNodeDefinition('foo');
        $def->values([]);
    }

    public function testGetNode(): void
    {
        $def = new EnumNodeDefinition('foo');
        $def->values(['foo', 'bar']);

        $node = $def->getNode();
        self::assertSame(['foo', 'bar'], $node->getValues());
    }
}
