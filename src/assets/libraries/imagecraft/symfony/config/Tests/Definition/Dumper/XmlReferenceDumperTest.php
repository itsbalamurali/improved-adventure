<?php



/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Config\Tests\Definition\Dumper;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\Definition\Dumper\XmlReferenceDumper;
use Symfony\Component\Config\Tests\Fixtures\Configuration\ExampleConfiguration;

/**
 * @internal
 *
 * @coversNothing
 */
final class XmlReferenceDumperTest extends TestCase
{
    public function testDumper(): void
    {
        $configuration = new ExampleConfiguration();

        $dumper = new XmlReferenceDumper();
        self::assertSame($this->getConfigurationAsString(), $dumper->dump($configuration));
    }

    public function testNamespaceDumper(): void
    {
        $configuration = new ExampleConfiguration();

        $dumper = new XmlReferenceDumper();
        self::assertSame(str_replace('http://example.org/schema/dic/acme_root', 'http://symfony.com/schema/dic/symfony', $this->getConfigurationAsString()), $dumper->dump($configuration, 'http://symfony.com/schema/dic/symfony'));
    }

    private function getConfigurationAsString()
    {
        return str_replace(
            "\n",
            PHP_EOL,
            <<<'EOL'
                <!-- Namespace: http://example.org/schema/dic/acme_root -->
                <!-- scalar-required: Required -->
                <!-- enum-with-default: One of "this"; "that" -->
                <!-- enum: One of "this"; "that" -->
                <config
                    boolean="true"
                    scalar-empty=""
                    scalar-null="null"
                    scalar-true="true"
                    scalar-false="false"
                    scalar-default="default"
                    scalar-array-empty=""
                    scalar-array-defaults="elem1,elem2"
                    scalar-required=""
                    node-with-a-looong-name=""
                    enum-with-default="this"
                    enum=""
                >

                    <!-- some info -->
                    <!--
                        child3: this is a long
                                multi-line info text
                                which should be indented;
                                Example: example setting
                    -->
                    <array
                        child1=""
                        child2=""
                        child3=""
                    />

                    <!-- prototype: Parameter name -->
                    <parameter name="parameter name">scalar value</parameter>

                    <!-- prototype -->
                    <connection
                        user=""
                        pass=""
                    />

                </config>

                EOL
        );
    }
}
