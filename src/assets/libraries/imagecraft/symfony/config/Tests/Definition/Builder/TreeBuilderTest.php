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
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Tests\Fixtures\Builder\NodeBuilder as CustomNodeBuilder;

/**
 * @internal
 *
 * @coversNothing
 */
final class TreeBuilderTest extends TestCase
{
    public function testUsingACustomNodeBuilder(): void
    {
        $builder = new TreeBuilder();
        $root = $builder->root('custom', 'array', new CustomNodeBuilder());

        $nodeBuilder = $root->children();

        self::assertInstanceOf('Symfony\Component\Config\Tests\Fixtures\Builder\NodeBuilder', $nodeBuilder);

        $nodeBuilder = $nodeBuilder->arrayNode('deeper')->children();

        self::assertInstanceOf('Symfony\Component\Config\Tests\Fixtures\Builder\NodeBuilder', $nodeBuilder);
    }

    public function testOverrideABuiltInNodeType(): void
    {
        $builder = new TreeBuilder();
        $root = $builder->root('override', 'array', new CustomNodeBuilder());

        $definition = $root->children()->variableNode('variable');

        self::assertInstanceOf('Symfony\Component\Config\Tests\Fixtures\Builder\VariableNodeDefinition', $definition);
    }

    public function testAddANodeType(): void
    {
        $builder = new TreeBuilder();
        $root = $builder->root('override', 'array', new CustomNodeBuilder());

        $definition = $root->children()->barNode('variable');

        self::assertInstanceOf('Symfony\Component\Config\Tests\Fixtures\Builder\BarNodeDefinition', $definition);
    }

    public function testCreateABuiltInNodeTypeWithACustomNodeBuilder(): void
    {
        $builder = new TreeBuilder();
        $root = $builder->root('builtin', 'array', new CustomNodeBuilder());

        $definition = $root->children()->booleanNode('boolean');

        self::assertInstanceOf('Symfony\Component\Config\Definition\Builder\BooleanNodeDefinition', $definition);
    }

    public function testPrototypedArrayNodeUseTheCustomNodeBuilder(): void
    {
        $builder = new TreeBuilder();
        $root = $builder->root('override', 'array', new CustomNodeBuilder());

        $root->prototype('bar')->end();

        self::assertInstanceOf('Symfony\Component\Config\Tests\Fixtures\BarNode', $root->getNode(true)->getPrototype());
    }

    public function testAnExtendedNodeBuilderGetsPropagatedToTheChildren(): void
    {
        $builder = new TreeBuilder();

        $builder->root('propagation')
            ->children()
            ->setNodeClass('extended', 'Symfony\Component\Config\Definition\Builder\BooleanNodeDefinition')
            ->node('foo', 'extended')->end()
            ->arrayNode('child')
            ->children()
            ->node('foo', 'extended')
            ->end()
            ->end()
            ->end()
            ->end()
        ;

        $node = $builder->buildTree();
        $children = $node->getChildren();

        self::assertInstanceOf('Symfony\Component\Config\Definition\BooleanNode', $children['foo']);

        $childChildren = $children['child']->getChildren();

        self::assertInstanceOf('Symfony\Component\Config\Definition\BooleanNode', $childChildren['foo']);
    }

    public function testDefinitionInfoGetsTransferredToNode(): void
    {
        $builder = new TreeBuilder();

        $builder->root('test')->info('root info')
            ->children()
            ->node('child', 'variable')->info('child info')->defaultValue('default')
            ->end()
            ->end()
        ;

        $tree = $builder->buildTree();
        $children = $tree->getChildren();

        self::assertSame('root info', $tree->getInfo());
        self::assertSame('child info', $children['child']->getInfo());
    }

    public function testDefinitionExampleGetsTransferredToNode(): void
    {
        $builder = new TreeBuilder();

        $builder->root('test')
            ->example(['key' => 'value'])
            ->children()
            ->node('child', 'variable')->info('child info')->defaultValue('default')->example('example')
            ->end()
            ->end()
        ;

        $tree = $builder->buildTree();
        $children = $tree->getChildren();

        self::assertIsArray($tree->getExample());
        self::assertSame('example', $children['child']->getExample());
    }
}
