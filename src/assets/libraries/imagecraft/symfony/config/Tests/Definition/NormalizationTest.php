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
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\Config\Definition\NodeInterface;

/**
 * @internal
 *
 * @coversNothing
 */
final class NormalizationTest extends TestCase
{
    /**
     * @dataProvider provideNormalizeEncodersCases
     *
     * @param mixed $denormalized
     */
    public function testNormalizeEncoders($denormalized): void
    {
        $tb = new TreeBuilder();
        $tree = $tb
            ->root('root_name', 'array')
            ->fixXmlConfig('encoder')
            ->children()
            ->node('encoders', 'array')
            ->useAttributeAsKey('class')
            ->prototype('array')
            ->beforeNormalization()->ifString()->then(static fn ($v) => ['algorithm' => $v])->end()
            ->children()
            ->node('algorithm', 'scalar')->end()
            ->end()
            ->end()
            ->end()
            ->end()
            ->end()
            ->buildTree()
        ;

        $normalized = [
            'encoders' => [
                'foo' => ['algorithm' => 'plaintext'],
            ],
        ];

        $this->assertNormalized($tree, $denormalized, $normalized);
    }

    public static function provideNormalizeEncodersCases(): iterable
    {
        $configs = [];

        // XML
        $configs[] = [
            'encoder' => [
                ['class' => 'foo', 'algorithm' => 'plaintext'],
            ],
        ];

        // XML when only one element of this type
        $configs[] = [
            'encoder' => ['class' => 'foo', 'algorithm' => 'plaintext'],
        ];

        // YAML/PHP
        $configs[] = [
            'encoders' => [
                ['class' => 'foo', 'algorithm' => 'plaintext'],
            ],
        ];

        // YAML/PHP
        $configs[] = [
            'encoders' => [
                'foo' => 'plaintext',
            ],
        ];

        // YAML/PHP
        $configs[] = [
            'encoders' => [
                'foo' => ['algorithm' => 'plaintext'],
            ],
        ];

        return array_map(static fn ($v) => [$v], $configs);
    }

    /**
     * @dataProvider provideAnonymousKeysArrayCases
     *
     * @param mixed $denormalized
     */
    public function testAnonymousKeysArray($denormalized): void
    {
        $tb = new TreeBuilder();
        $tree = $tb
            ->root('root', 'array')
            ->children()
            ->node('logout', 'array')
            ->fixXmlConfig('handler')
            ->children()
            ->node('handlers', 'array')
            ->prototype('scalar')->end()
            ->end()
            ->end()
            ->end()
            ->end()
            ->end()
            ->buildTree()
        ;

        $normalized = ['logout' => ['handlers' => ['a', 'b', 'c']]];

        $this->assertNormalized($tree, $denormalized, $normalized);
    }

    public static function provideAnonymousKeysArrayCases(): iterable
    {
        $configs = [];

        $configs[] = [
            'logout' => [
                'handlers' => ['a', 'b', 'c'],
            ],
        ];

        $configs[] = [
            'logout' => [
                'handler' => ['a', 'b', 'c'],
            ],
        ];

        return array_map(static fn ($v) => [$v], $configs);
    }

    /**
     * @dataProvider provideNumericKeysAsAttributesCases
     *
     * @param mixed $denormalized
     */
    public function testNumericKeysAsAttributes($denormalized): void
    {
        $normalized = [
            'thing' => [42 => ['foo', 'bar'], 1_337 => ['baz', 'qux']],
        ];

        $this->assertNormalized($this->getNumericKeysTestTree(), $denormalized, $normalized);
    }

    public static function provideNumericKeysAsAttributesCases(): iterable
    {
        $configs = [];

        $configs[] = [
            'thing' => [
                42 => ['foo', 'bar'], 1_337 => ['baz', 'qux'],
            ],
        ];

        $configs[] = [
            'thing' => [
                ['foo', 'bar', 'id' => 42], ['baz', 'qux', 'id' => 1_337],
            ],
        ];

        return array_map(static fn ($v) => [$v], $configs);
    }

    public function testNonAssociativeArrayThrowsExceptionIfAttributeNotSet(): void
    {
        $this->expectException(InvalidConfigurationException::class);
        $this->expectExceptionMessage('The attribute "id" must be set for path "root.thing".');

        $denormalized = [
            'thing' => [
                ['foo', 'bar'], ['baz', 'qux'],
            ],
        ];

        $this->assertNormalized($this->getNumericKeysTestTree(), $denormalized, []);
    }

    public function testAssociativeArrayPreserveKeys(): void
    {
        $tb = new TreeBuilder();
        $tree = $tb
            ->root('root', 'array')
            ->prototype('array')
            ->children()
            ->node('foo', 'scalar')->end()
            ->end()
            ->end()
            ->end()
            ->buildTree()
        ;

        $data = ['first' => ['foo' => 'bar']];

        $this->assertNormalized($tree, $data, $data);
    }

    public static function assertNormalized(NodeInterface $tree, $denormalized, $normalized): void
    {
        self::assertSame($normalized, $tree->normalize($denormalized));
    }

    private function getNumericKeysTestTree()
    {
        $tb = new TreeBuilder();

        return $tb
            ->root('root', 'array')
            ->children()
            ->node('thing', 'array')
            ->useAttributeAsKey('id')
            ->prototype('array')
            ->prototype('scalar')->end()
            ->end()
            ->end()
            ->end()
            ->end()
            ->buildTree()
        ;
    }
}
