<?php



/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Translation\Tests\Dumper;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Translation\Dumper\JsonFileDumper;
use Symfony\Component\Translation\MessageCatalogue;

/**
 * @internal
 *
 * @coversNothing
 */
final class JsonFileDumperTest extends TestCase
{
    public function testFormatCatalogue(): void
    {
        if (\PHP_VERSION_ID < 50_400) {
            self::markTestIncomplete('PHP below 5.4 doesn\'t support JSON pretty printing');
        }

        $catalogue = new MessageCatalogue('en');
        $catalogue->add(['foo' => 'bar']);

        $dumper = new JsonFileDumper();

        self::assertStringEqualsFile(__DIR__.'/../fixtures/resources.json', $dumper->formatCatalogue($catalogue, 'messages'));
    }

    public function testDumpWithCustomEncoding(): void
    {
        $catalogue = new MessageCatalogue('en');
        $catalogue->add(['foo' => '"bar"']);

        $dumper = new JsonFileDumper();

        self::assertStringEqualsFile(__DIR__.'/../fixtures/resources.dump.json', $dumper->formatCatalogue($catalogue, 'messages', ['json_encoding' => JSON_HEX_QUOT]));
    }
}
