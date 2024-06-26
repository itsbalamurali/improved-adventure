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
use Symfony\Component\Config\Definition\Builder\FloatNodeDefinition;
use Symfony\Component\Config\Definition\Builder\IntegerNodeDefinition;
use Symfony\Component\Config\Definition\Builder\IntegerNodeDefinition as NumericNodeDefinition;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

/**
 * @internal
 *
 * @coversNothing
 */
final class NumericNodeDefinitionTest extends TestCase
{
    public function testIncoherentMinAssertion(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('You cannot define a min(4) as you already have a max(3)');

        $def = new NumericNodeDefinition('foo');
        $def->max(3)->min(4);
    }

    public function testIncoherentMaxAssertion(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('You cannot define a max(2) as you already have a min(3)');

        $node = new NumericNodeDefinition('foo');
        $node->min(3)->max(2);
    }

    public function testIntegerMinAssertion(): void
    {
        $this->expectException(InvalidConfigurationException::class);
        $this->expectExceptionMessage('The value 4 is too small for path "foo". Should be greater than or equal to 5');

        $def = new IntegerNodeDefinition('foo');
        $def->min(5)->getNode()->finalize(4);
    }

    public function testIntegerMaxAssertion(): void
    {
        $this->expectException(InvalidConfigurationException::class);
        $this->expectExceptionMessage('The value 4 is too big for path "foo". Should be less than or equal to 3');

        $def = new IntegerNodeDefinition('foo');
        $def->max(3)->getNode()->finalize(4);
    }

    public function testIntegerValidMinMaxAssertion(): void
    {
        $def = new IntegerNodeDefinition('foo');
        $node = $def->min(3)->max(7)->getNode();
        self::assertSame(4, $node->finalize(4));
    }

    public function testFloatMinAssertion(): void
    {
        $this->expectException(InvalidConfigurationException::class);
        $this->expectExceptionMessage('The value 400 is too small for path "foo". Should be greater than or equal to 500');

        $def = new FloatNodeDefinition('foo');
        $def->min(5E2)->getNode()->finalize(4e2);
    }

    public function testFloatMaxAssertion(): void
    {
        $this->expectException(InvalidConfigurationException::class);
        $this->expectExceptionMessage('The value 4.3 is too big for path "foo". Should be less than or equal to 0.3');

        $def = new FloatNodeDefinition('foo');
        $def->max(0.3)->getNode()->finalize(4.3);
    }

    public function testFloatValidMinMaxAssertion(): void
    {
        $def = new FloatNodeDefinition('foo');
        $node = $def->min(3.0)->max(7e2)->getNode();
        self::assertSame(4.5, $node->finalize(4.5));
    }
}
