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
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\Exception\ForbiddenOverwriteException;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

/**
 * @internal
 *
 * @coversNothing
 */
final class MergeTest extends TestCase
{
    public function testForbiddenOverwrite(): void
    {
        $this->expectException(ForbiddenOverwriteException::class);

        $tb = new TreeBuilder();
        $tree = $tb
            ->root('root', 'array')
            ->children()
            ->node('foo', 'scalar')
            ->cannotBeOverwritten()
            ->end()
            ->end()
            ->end()
            ->buildTree()
        ;

        $a = [
            'foo' => 'bar',
        ];

        $b = [
            'foo' => 'moo',
        ];

        $tree->merge($a, $b);
    }

    public function testUnsetKey(): void
    {
        $tb = new TreeBuilder();
        $tree = $tb
            ->root('root', 'array')
            ->children()
            ->node('foo', 'scalar')->end()
            ->node('bar', 'scalar')->end()
            ->node('unsettable', 'array')
            ->canBeUnset()
            ->children()
            ->node('foo', 'scalar')->end()
            ->node('bar', 'scalar')->end()
            ->end()
            ->end()
            ->node('unsetted', 'array')
            ->canBeUnset()
            ->prototype('scalar')->end()
            ->end()
            ->end()
            ->end()
            ->buildTree()
        ;

        $a = [
            'foo' => 'bar',
            'unsettable' => [
                'foo' => 'a',
                'bar' => 'b',
            ],
            'unsetted' => false,
        ];

        $b = [
            'foo' => 'moo',
            'bar' => 'b',
            'unsettable' => false,
            'unsetted' => ['a', 'b'],
        ];

        self::assertSame([
            'foo' => 'moo',
            'bar' => 'b',
            'unsettable' => false,
            'unsetted' => ['a', 'b'],
        ], $tree->merge($a, $b));
    }

    public function testDoesNotAllowNewKeysInSubsequentConfigs(): void
    {
        $this->expectException(InvalidConfigurationException::class);

        $tb = new TreeBuilder();
        $tree = $tb
            ->root('config', 'array')
            ->children()
            ->node('test', 'array')
            ->disallowNewKeysInSubsequentConfigs()
            ->useAttributeAsKey('key')
            ->prototype('array')
            ->children()
            ->node('value', 'scalar')->end()
            ->end()
            ->end()
            ->end()
            ->end()
            ->end()
            ->buildTree()
        ;

        $a = [
            'test' => [
                'a' => ['value' => 'foo'],
            ],
        ];

        $b = [
            'test' => [
                'b' => ['value' => 'foo'],
            ],
        ];

        $tree->merge($a, $b);
    }

    public function testPerformsNoDeepMerging(): void
    {
        $tb = new TreeBuilder();

        $tree = $tb
            ->root('config', 'array')
            ->children()
            ->node('no_deep_merging', 'array')
            ->performNoDeepMerging()
            ->children()
            ->node('foo', 'scalar')->end()
            ->node('bar', 'scalar')->end()
            ->end()
            ->end()
            ->end()
            ->end()
            ->buildTree()
        ;

        $a = [
            'no_deep_merging' => [
                'foo' => 'a',
                'bar' => 'b',
            ],
        ];

        $b = [
            'no_deep_merging' => [
                'c' => 'd',
            ],
        ];

        self::assertSame([
            'no_deep_merging' => [
                'c' => 'd',
            ],
        ], $tree->merge($a, $b));
    }

    public function testPrototypeWithoutAKeyAttribute(): void
    {
        $tb = new TreeBuilder();

        $tree = $tb
            ->root('config', 'array')
            ->children()
            ->arrayNode('append_elements')
            ->prototype('scalar')->end()
            ->end()
            ->end()
            ->end()
            ->buildTree()
        ;

        $a = [
            'append_elements' => ['a', 'b'],
        ];

        $b = [
            'append_elements' => ['c', 'd'],
        ];

        self::assertSame(['append_elements' => ['a', 'b', 'c', 'd']], $tree->merge($a, $b));
    }
}
