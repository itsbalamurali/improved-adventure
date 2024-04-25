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
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\ScalarNodeDefinition;
use Symfony\Component\Config\Definition\Exception\InvalidDefinitionException;
use Symfony\Component\Config\Definition\Processor;

/**
 * @internal
 *
 * @coversNothing
 */
final class ArrayNodeDefinitionTest extends TestCase
{
    public function testAppendingSomeNode(): void
    {
        $parent = new ArrayNodeDefinition('root');
        $child = new ScalarNodeDefinition('child');

        $parent
            ->children()
            ->scalarNode('foo')->end()
            ->scalarNode('bar')->end()
            ->end()
            ->append($child)
        ;

        self::assertCount(3, $this->getField($parent, 'children'));
        self::assertContains($child, $this->getField($parent, 'children'));
    }

    /**
     * @dataProvider providePrototypeNodeSpecificOptionCases
     *
     * @param mixed $method
     * @param mixed $args
     */
    public function testPrototypeNodeSpecificOption($method, $args): void
    {
        $this->expectException(InvalidDefinitionException::class);

        $node = new ArrayNodeDefinition('root');

        \call_user_func_array([$node, $method], $args);

        $node->getNode();
    }

    public static function providePrototypeNodeSpecificOptionCases(): iterable
    {
        return [
            ['defaultValue', [[]]],
            ['addDefaultChildrenIfNoneSet', []],
            ['requiresAtLeastOneElement', []],
            ['useAttributeAsKey', ['foo']],
        ];
    }

    public function testConcreteNodeSpecificOption(): void
    {
        $this->expectException(InvalidDefinitionException::class);

        $node = new ArrayNodeDefinition('root');
        $node
            ->addDefaultsIfNotSet()
            ->prototype('array')
        ;
        $node->getNode();
    }

    public function testPrototypeNodesCantHaveADefaultValueWhenUsingDefaultChildren(): void
    {
        $this->expectException(InvalidDefinitionException::class);

        $node = new ArrayNodeDefinition('root');
        $node
            ->defaultValue([])
            ->addDefaultChildrenIfNoneSet('foo')
            ->prototype('array')
        ;
        $node->getNode();
    }

    public function testPrototypedArrayNodeDefaultWhenUsingDefaultChildren(): void
    {
        $node = new ArrayNodeDefinition('root');
        $node
            ->addDefaultChildrenIfNoneSet()
            ->prototype('array')
        ;
        $tree = $node->getNode();
        self::assertSame([[]], $tree->getDefaultValue());
    }

    /**
     * @dataProvider providePrototypedArrayNodeDefaultCases
     *
     * @param mixed $args
     * @param mixed $shouldThrowWhenUsingAttrAsKey
     * @param mixed $shouldThrowWhenNotUsingAttrAsKey
     * @param mixed $defaults
     */
    public function testPrototypedArrayNodeDefault($args, $shouldThrowWhenUsingAttrAsKey, $shouldThrowWhenNotUsingAttrAsKey, $defaults): void
    {
        $node = new ArrayNodeDefinition('root');
        $node
            ->addDefaultChildrenIfNoneSet($args)
            ->prototype('array')
        ;

        try {
            $tree = $node->getNode();
            self::assertFalse($shouldThrowWhenNotUsingAttrAsKey);
            self::assertSame($defaults, $tree->getDefaultValue());
        } catch (InvalidDefinitionException $e) {
            self::assertTrue($shouldThrowWhenNotUsingAttrAsKey);
        }

        $node = new ArrayNodeDefinition('root');
        $node
            ->useAttributeAsKey('attr')
            ->addDefaultChildrenIfNoneSet($args)
            ->prototype('array')
        ;

        try {
            $tree = $node->getNode();
            self::assertFalse($shouldThrowWhenUsingAttrAsKey);
            self::assertSame($defaults, $tree->getDefaultValue());
        } catch (InvalidDefinitionException $e) {
            self::assertTrue($shouldThrowWhenUsingAttrAsKey);
        }
    }

    public static function providePrototypedArrayNodeDefaultCases(): iterable
    {
        return [
            [null, true, false, [[]]],
            [2, true, false, [[], []]],
            ['2', false, true, ['2' => []]],
            ['foo', false, true, ['foo' => []]],
            [['foo'], false, true, ['foo' => []]],
            [['foo', 'bar'], false, true, ['foo' => [], 'bar' => []]],
        ];
    }

    public function testNestedPrototypedArrayNodes(): void
    {
        $nodeDefinition = new ArrayNodeDefinition('root');
        $nodeDefinition
            ->addDefaultChildrenIfNoneSet()
            ->prototype('array')
            ->prototype('array')
        ;
        $node = $nodeDefinition->getNode();

        self::assertInstanceOf('Symfony\Component\Config\Definition\PrototypedArrayNode', $node);
        self::assertInstanceOf('Symfony\Component\Config\Definition\PrototypedArrayNode', $node->getPrototype());
    }

    public function testEnabledNodeDefaults(): void
    {
        $node = new ArrayNodeDefinition('root');
        $node
            ->canBeEnabled()
            ->children()
            ->scalarNode('foo')->defaultValue('bar')->end()
        ;

        self::assertSame(['enabled' => false, 'foo' => 'bar'], $node->getNode()->getDefaultValue());
    }

    /**
     * @dataProvider provideTrueEnableEnabledNodeCases
     *
     * @param mixed $expected
     * @param mixed $config
     * @param mixed $message
     */
    public function testTrueEnableEnabledNode($expected, $config, $message): void
    {
        $processor = new Processor();
        $node = new ArrayNodeDefinition('root');
        $node
            ->canBeEnabled()
            ->children()
            ->scalarNode('foo')->defaultValue('bar')->end()
        ;

        self::assertSame(
            $expected,
            $processor->process($node->getNode(), $config),
            $message
        );
    }

    public function testCanBeDisabled(): void
    {
        $node = new ArrayNodeDefinition('root');
        $node->canBeDisabled();

        self::assertTrue($this->getField($node, 'addDefaults'));
        self::assertSame(['enabled' => false], $this->getField($node, 'falseEquivalent'));
        self::assertSame(['enabled' => true], $this->getField($node, 'trueEquivalent'));
        self::assertSame(['enabled' => true], $this->getField($node, 'nullEquivalent'));

        $nodeChildren = $this->getField($node, 'children');
        self::assertArrayHasKey('enabled', $nodeChildren);

        $enabledNode = $nodeChildren['enabled'];
        self::assertTrue($this->getField($enabledNode, 'default'));
        self::assertTrue($this->getField($enabledNode, 'defaultValue'));
    }

    public function testIgnoreExtraKeys(): void
    {
        $node = new ArrayNodeDefinition('root');

        self::assertFalse($this->getField($node, 'ignoreExtraKeys'));

        $result = $node->ignoreExtraKeys();

        self::assertSame($node, $result);
        self::assertTrue($this->getField($node, 'ignoreExtraKeys'));
    }

    public function testNormalizeKeys(): void
    {
        $node = new ArrayNodeDefinition('root');

        self::assertTrue($this->getField($node, 'normalizeKeys'));

        $result = $node->normalizeKeys(false);

        self::assertSame($node, $result);
        self::assertFalse($this->getField($node, 'normalizeKeys'));
    }

    public function testUnsetChild(): void
    {
        $node = new ArrayNodeDefinition('root');
        $node
            ->children()
            ->scalarNode('value')
            ->beforeNormalization()
            ->ifTrue(static fn ($value) => empty($value))
            ->thenUnset()
            ->end()
            ->end()
            ->end()
        ;

        self::assertSame([], $node->getNode()->normalize(['value' => null]));
    }

    public static function provideTrueEnableEnabledNodeCases(): iterable
    {
        return [
            [['enabled' => true, 'foo' => 'bar'], [true], 'true enables an enableable node'],
            [['enabled' => true, 'foo' => 'bar'], [null], 'null enables an enableable node'],
            [['enabled' => true, 'foo' => 'bar'], [['enabled' => true]], 'An enableable node can be enabled'],
            [['enabled' => true, 'foo' => 'baz'], [['foo' => 'baz']], 'any configuration enables an enableable node'],
            [['enabled' => false, 'foo' => 'baz'], [['foo' => 'baz', 'enabled' => false]], 'An enableable node can be disabled'],
            [['enabled' => false, 'foo' => 'bar'], [false], 'false disables an enableable node'],
        ];
    }

    protected function getField($object, $field)
    {
        $reflection = new \ReflectionProperty($object, $field);
        $reflection->setAccessible(true);

        return $reflection->getValue($object);
    }
}
