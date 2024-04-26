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
use Symfony\Component\Config\Definition\ArrayNode;
use Symfony\Component\Config\Definition\PrototypedArrayNode;
use Symfony\Component\Config\Definition\ScalarNode;
use Symfony\Component\Config\Definition\VariableNode;

/**
 * @internal
 *
 * @coversNothing
 */
final class PrototypedArrayNodeTest extends TestCase
{
    public function testGetDefaultValueReturnsAnEmptyArrayForPrototypes(): void
    {
        $node = new PrototypedArrayNode('root');
        $prototype = new ArrayNode(null, $node);
        $node->setPrototype($prototype);
        self::assertEmpty($node->getDefaultValue());
    }

    public function testGetDefaultValueReturnsDefaultValueForPrototypes(): void
    {
        $node = new PrototypedArrayNode('root');
        $prototype = new ArrayNode(null, $node);
        $node->setPrototype($prototype);
        $node->setDefaultValue(['test']);
        self::assertSame(['test'], $node->getDefaultValue());
    }

    // a remapped key (e.g. "mapping" -> "mappings") should be unset after being used
    public function testRemappedKeysAreUnset(): void
    {
        $node = new ArrayNode('root');
        $mappingsNode = new PrototypedArrayNode('mappings');
        $node->addChild($mappingsNode);

        // each item under mappings is just a scalar
        $prototype = new ScalarNode(null, $mappingsNode);
        $mappingsNode->setPrototype($prototype);

        $remappings = [];
        $remappings[] = ['mapping', 'mappings'];
        $node->setXmlRemappings($remappings);

        $normalized = $node->normalize(['mapping' => ['foo', 'bar']]);
        self::assertSame(['mappings' => ['foo', 'bar']], $normalized);
    }

    /**
     * Tests that when a key attribute is mapped, that key is removed from the array.
     *
     *     <things>
     *         <option id="option1" value="foo">
     *         <option id="option2" value="bar">
     *     </things>
     *
     * The above should finally be mapped to an array that looks like this
     * (because "id" is the key attribute).
     *
     *     array(
     *         'things' => array(
     *             'option1' => 'foo',
     *             'option2' => 'bar',
     *         )
     *     )
     */
    public function testMappedAttributeKeyIsRemoved(): void
    {
        $node = new PrototypedArrayNode('root');
        $node->setKeyAttribute('id', true);

        // each item under the root is an array, with one scalar item
        $prototype = new ArrayNode(null, $node);
        $prototype->addChild(new ScalarNode('foo'));
        $node->setPrototype($prototype);

        $children = [];
        $children[] = ['id' => 'item_name', 'foo' => 'bar'];
        $normalized = $node->normalize($children);

        $expected = [];
        $expected['item_name'] = ['foo' => 'bar'];
        self::assertSame($expected, $normalized);
    }

    /**
     * Tests the opposite of the testMappedAttributeKeyIsRemoved because
     * the removal can be toggled with an option.
     */
    public function testMappedAttributeKeyNotRemoved(): void
    {
        $node = new PrototypedArrayNode('root');
        $node->setKeyAttribute('id', false);

        // each item under the root is an array, with two scalar items
        $prototype = new ArrayNode(null, $node);
        $prototype->addChild(new ScalarNode('foo'));
        $prototype->addChild(new ScalarNode('id')); // the key attribute will remain
        $node->setPrototype($prototype);

        $children = [];
        $children[] = ['id' => 'item_name', 'foo' => 'bar'];
        $normalized = $node->normalize($children);

        $expected = [];
        $expected['item_name'] = ['id' => 'item_name', 'foo' => 'bar'];
        self::assertSame($expected, $normalized);
    }

    public function testAddDefaultChildren(): void
    {
        $node = $this->getPrototypeNodeWithDefaultChildren();
        $node->setAddChildrenIfNoneSet();
        self::assertTrue($node->hasDefaultValue());
        self::assertSame([['foo' => 'bar']], $node->getDefaultValue());

        $node = $this->getPrototypeNodeWithDefaultChildren();
        $node->setKeyAttribute('foobar');
        $node->setAddChildrenIfNoneSet();
        self::assertTrue($node->hasDefaultValue());
        self::assertSame(['defaults' => ['foo' => 'bar']], $node->getDefaultValue());

        $node = $this->getPrototypeNodeWithDefaultChildren();
        $node->setKeyAttribute('foobar');
        $node->setAddChildrenIfNoneSet('defaultkey');
        self::assertTrue($node->hasDefaultValue());
        self::assertSame(['defaultkey' => ['foo' => 'bar']], $node->getDefaultValue());

        $node = $this->getPrototypeNodeWithDefaultChildren();
        $node->setKeyAttribute('foobar');
        $node->setAddChildrenIfNoneSet(['defaultkey']);
        self::assertTrue($node->hasDefaultValue());
        self::assertSame(['defaultkey' => ['foo' => 'bar']], $node->getDefaultValue());

        $node = $this->getPrototypeNodeWithDefaultChildren();
        $node->setKeyAttribute('foobar');
        $node->setAddChildrenIfNoneSet(['dk1', 'dk2']);
        self::assertTrue($node->hasDefaultValue());
        self::assertSame(['dk1' => ['foo' => 'bar'], 'dk2' => ['foo' => 'bar']], $node->getDefaultValue());

        $node = $this->getPrototypeNodeWithDefaultChildren();
        $node->setAddChildrenIfNoneSet([5, 6]);
        self::assertTrue($node->hasDefaultValue());
        self::assertSame([0 => ['foo' => 'bar'], 1 => ['foo' => 'bar']], $node->getDefaultValue());

        $node = $this->getPrototypeNodeWithDefaultChildren();
        $node->setAddChildrenIfNoneSet(2);
        self::assertTrue($node->hasDefaultValue());
        self::assertSame([['foo' => 'bar'], ['foo' => 'bar']], $node->getDefaultValue());
    }

    public function testDefaultChildrenWinsOverDefaultValue(): void
    {
        $node = $this->getPrototypeNodeWithDefaultChildren();
        $node->setAddChildrenIfNoneSet();
        $node->setDefaultValue(['bar' => 'foo']);
        self::assertTrue($node->hasDefaultValue());
        self::assertSame([['foo' => 'bar']], $node->getDefaultValue());
    }

    /**
     * Tests that when a key attribute is mapped, that key is removed from the array.
     * And if only 'value' element is left in the array, it will replace its wrapper array.
     *
     *     <things>
     *         <option id="option1" value="value1">
     *     </things>
     *
     * The above should finally be mapped to an array that looks like this
     * (because "id" is the key attribute).
     *
     *     array(
     *         'things' => array(
     *             'option1' => 'value1'
     *         )
     *     )
     *
     * It's also possible to mix 'value-only' and 'non-value-only' elements in the array.
     *
     * <things>
     *     <option id="option1" value="value1">
     *     <option id="option2" value="value2" foo="foo2">
     * </things>
     *
     * The above should finally be mapped to an array as follows
     *
     * array(
     *     'things' => array(
     *         'option1' => 'value1',
     *         'option2' => array(
     *             'value' => 'value2',
     *             'foo' => 'foo2'
     *         )
     *     )
     * )
     *
     * The 'value' element can also be ArrayNode:
     *
     * <things>
     *     <option id="option1">
     *         <value>
     *            <foo>foo1</foo>
     *            <bar>bar1</bar>
     *         </value>
     *     </option>
     * </things>
     *
     * The above should be finally be mapped to an array as follows
     *
     * array(
     *     'things' => array(
     *         'option1' => array(
     *             'foo' => 'foo1',
     *             'bar' => 'bar1'
     *         )
     *     )
     * )
     *
     * If using VariableNode for value node, it's also possible to mix different types of value nodes:
     *
     * <things>
     *     <option id="option1">
     *         <value>
     *            <foo>foo1</foo>
     *            <bar>bar1</bar>
     *         </value>
     *     </option>
     *     <option id="option2" value="value2">
     * </things>
     *
     * The above should be finally mapped to an array as follows
     *
     * array(
     *     'things' => array(
     *         'option1' => array(
     *             'foo' => 'foo1',
     *             'bar' => 'bar1'
     *         ),
     *         'option2' => 'value2'
     *     )
     * )
     *
     * @dataProvider provideMappedAttributeKeyIsRemovedLeftValueOnlyCases
     *
     * @param mixed $value
     * @param mixed $children
     * @param mixed $expected
     */
    public function testMappedAttributeKeyIsRemovedLeftValueOnly($value, $children, $expected): void
    {
        $node = new PrototypedArrayNode('root');
        $node->setKeyAttribute('id', true);

        // each item under the root is an array, with one scalar item
        $prototype = new ArrayNode(null, $node);
        $prototype->addChild(new ScalarNode('id'));
        $prototype->addChild(new ScalarNode('foo'));
        $prototype->addChild($value);
        $node->setPrototype($prototype);

        $normalized = $node->normalize($children);
        self::assertSame($expected, $normalized);
    }

    public static function provideMappedAttributeKeyIsRemovedLeftValueOnlyCases(): iterable
    {
        $scalarValue = new ScalarNode('value');

        $arrayValue = new ArrayNode('value');
        $arrayValue->addChild(new ScalarNode('foo'));
        $arrayValue->addChild(new ScalarNode('bar'));

        $variableValue = new VariableNode('value');

        return [
            [
                $scalarValue,
                [
                    ['id' => 'option1', 'value' => 'value1'],
                ],
                ['option1' => 'value1'],
            ],

            [
                $scalarValue,
                [
                    ['id' => 'option1', 'value' => 'value1'],
                    ['id' => 'option2', 'value' => 'value2', 'foo' => 'foo2'],
                ],
                [
                    'option1' => 'value1',
                    'option2' => ['value' => 'value2', 'foo' => 'foo2'],
                ],
            ],

            [
                $arrayValue,
                [
                    [
                        'id' => 'option1',
                        'value' => ['foo' => 'foo1', 'bar' => 'bar1'],
                    ],
                ],
                [
                    'option1' => ['foo' => 'foo1', 'bar' => 'bar1'],
                ],
            ],

            [$variableValue,
                [
                    [
                        'id' => 'option1', 'value' => ['foo' => 'foo1', 'bar' => 'bar1'],
                    ],
                    ['id' => 'option2', 'value' => 'value2'],
                ],
                [
                    'option1' => ['foo' => 'foo1', 'bar' => 'bar1'],
                    'option2' => 'value2',
                ],
            ],
        ];
    }

    protected function getPrototypeNodeWithDefaultChildren()
    {
        $node = new PrototypedArrayNode('root');
        $prototype = new ArrayNode(null, $node);
        $child = new ScalarNode('foo');
        $child->setDefaultValue('bar');
        $prototype->addChild($child);
        $prototype->setAddIfNotSet(true);
        $node->setPrototype($prototype);

        return $node;
    }
}
