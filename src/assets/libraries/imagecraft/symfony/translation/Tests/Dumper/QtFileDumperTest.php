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
use Symfony\Component\Translation\Dumper\QtFileDumper;
use Symfony\Component\Translation\MessageCatalogue;

/**
 * @internal
 *
 * @coversNothing
 */
final class QtFileDumperTest extends TestCase
{
    public function testFormatCatalogue(): void
    {
        $catalogue = new MessageCatalogue('en');
        $catalogue->add(['foo' => 'bar'], 'resources');

        $dumper = new QtFileDumper();

        self::assertStringEqualsFile(__DIR__.'/../fixtures/resources.ts', $dumper->formatCatalogue($catalogue, 'resources'));
    }
}
