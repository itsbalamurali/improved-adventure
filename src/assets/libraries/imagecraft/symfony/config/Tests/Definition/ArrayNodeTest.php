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
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\Config\Definition\Exception\InvalidTypeException;
use Symfony\Component\Config\Definition\ScalarNode;

/**
 * @internal
 *
 * @coversNothing
 */
final class ArrayNodeTest extends TestCase
{
    public function testNormalizeThrowsExceptionWhenFalseIsNotAllowed(): void
    {
        $this->expectException(InvalidTypeException::class);

        $node = new ArrayNode('root');
        $node->normalize(false);
    }

    public function testExceptionThrownOnUnrecognizedChild(): void
    {
        $this->expectException(InvalidConfigurationException::class);
        $this->expectExceptionMessage('Unrecognized option "foo" under "root"');

        $node = new ArrayNode('root');
        $node->normalize(['foo' => 'bar']);
    }

    public static function provideIgnoreAndRemoveBehaviorsCases(): iterable
    {
        $unrecognizedOptionException = new InvalidConfigurationException('Unrecognized option "foo" under "root"');

        return [
            [true, true, [], 'no exception is thrown for an unrecognized child if the ignoreExtraKeys option is set to true'],
            [true, false, ['foo' => 'bar'], 'extra keys are not removed when ignoreExtraKeys second option is set to false'],
            [false, true, $unrecognizedOptionException],
            [false, false, $unrecognizedOptionException],
        ];
    }

    /**
     * @dataProvider provideIgnoreAndRemoveBehaviorsCases
     *
     * @param mixed $ignore
     * @param mixed $remove
     * @param mixed $expected
     * @param mixed $message
     */
    public function testIgnoreAndRemoveBehaviors($ignore, $remove, $expected, $message = ''): void
    {
        if ($expected instanceof \Exception) {
            if (method_exists($this, 'expectException')) {
                $this->expectException(\get_class($expected));
                $this->expectExceptionMessage($expected->getMessage());
            } else {
                $this->expectException(\get_class($expected));
                $this->expectExceptionMessage($expected->getMessage());
            }
        }
        $node = new ArrayNode('root');
        $node->setIgnoreExtraKeys($ignore, $remove);
        $result = $node->normalize(['foo' => 'bar']);
        self::assertSame($expected, $result, $message);
    }

    /**
     * @dataProvider providePreNormalizeCases
     *
     * @param mixed $denormalized
     * @param mixed $normalized
     */
    public function testPreNormalize($denormalized, $normalized): void
    {
        $node = new ArrayNode('foo');

        $r = new \ReflectionMethod($node, 'preNormalize');
        $r->setAccessible(true);

        self::assertSame($normalized, $r->invoke($node, $denormalized));
    }

    public static function providePreNormalizeCases(): iterable
    {
        return [
            [
                ['foo-bar' => 'foo'],
                ['foo_bar' => 'foo'],
            ],
            [
                ['foo-bar_moo' => 'foo'],
                ['foo-bar_moo' => 'foo'],
            ],
            [
                ['anything-with-dash-and-no-underscore' => 'first', 'no_dash' => 'second'],
                ['anything_with_dash_and_no_underscore' => 'first', 'no_dash' => 'second'],
            ],
            [
                ['foo-bar' => null, 'foo_bar' => 'foo'],
                ['foo-bar' => null, 'foo_bar' => 'foo'],
            ],
        ];
    }

    /**
     * @dataProvider provideNodeNameCanBeZeroCases
     *
     * @param mixed $denormalized
     * @param mixed $normalized
     */
    public function testNodeNameCanBeZero($denormalized, $normalized): void
    {
        $zeroNode = new ArrayNode(0);
        $zeroNode->addChild(new ScalarNode('name'));
        $fiveNode = new ArrayNode(5);
        $fiveNode->addChild(new ScalarNode(0));
        $fiveNode->addChild(new ScalarNode('new_key'));
        $rootNode = new ArrayNode('root');
        $rootNode->addChild($zeroNode);
        $rootNode->addChild($fiveNode);
        $rootNode->addChild(new ScalarNode('string_key'));
        $r = new \ReflectionMethod($rootNode, 'normalizeValue');
        $r->setAccessible(true);

        self::assertSame($normalized, $r->invoke($rootNode, $denormalized));
    }

    public static function provideNodeNameCanBeZeroCases(): iterable
    {
        return [
            [
                [
                    0 => [
                        'name' => 'something',
                    ],
                    5 => [
                        0 => 'this won\'t work too',
                        'new_key' => 'some other value',
                    ],
                    'string_key' => 'just value',
                ],
                [
                    0 => [
                        'name' => 'something',
                    ],
                    5 => [
                        0 => 'this won\'t work too',
                        'new_key' => 'some other value',
                    ],
                    'string_key' => 'just value',
                ],
            ],
        ];
    }

    /**
     * @dataProvider provideChildrenOrderIsMaintainedOnNormalizeValueCases
     *
     * @param mixed $prenormalized
     * @param mixed $normalized
     */
    public function testChildrenOrderIsMaintainedOnNormalizeValue($prenormalized, $normalized): void
    {
        $scalar1 = new ScalarNode('1');
        $scalar2 = new ScalarNode('2');
        $scalar3 = new ScalarNode('3');
        $node = new ArrayNode('foo');
        $node->addChild($scalar1);
        $node->addChild($scalar3);
        $node->addChild($scalar2);

        $r = new \ReflectionMethod($node, 'normalizeValue');
        $r->setAccessible(true);

        self::assertSame($normalized, $r->invoke($node, $prenormalized));
    }

    public static function provideChildrenOrderIsMaintainedOnNormalizeValueCases(): iterable
    {
        return [
            [
                ['2' => 'two', '1' => 'one', '3' => 'three'],
                ['2' => 'two', '1' => 'one', '3' => 'three'],
            ],
        ];
    }

    public function testAddChildEmptyName(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Child nodes must be named.');

        $node = new ArrayNode('root');

        $childNode = new ArrayNode('');
        $node->addChild($childNode);
    }

    public function testAddChildNameAlreadyExists(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('A child node named "foo" already exists.');

        $node = new ArrayNode('root');

        $childNode = new ArrayNode('foo');
        $node->addChild($childNode);

        $childNodeWithSameName = new ArrayNode('foo');
        $node->addChild($childNodeWithSameName);
    }

    public function testGetDefaultValueWithoutDefaultValue(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('The node at path "foo" has no default value.');

        $node = new ArrayNode('foo');
        $node->getDefaultValue();
    }
}
