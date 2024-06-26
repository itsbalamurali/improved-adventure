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
use Symfony\Component\Translation\Dumper\FileDumper;
use Symfony\Component\Translation\MessageCatalogue;

/**
 * @internal
 *
 * @coversNothing
 */
final class FileDumperTest extends TestCase
{
    public function testDump(): void
    {
        $tempDir = sys_get_temp_dir();

        $catalogue = new MessageCatalogue('en');
        $catalogue->add(['foo' => 'bar']);

        $dumper = new ConcreteFileDumper();
        $dumper->dump($catalogue, ['path' => $tempDir]);

        self::assertFileExists($tempDir.'/messages.en.concrete');
    }

    public function testDumpBackupsFileIfExisting(): void
    {
        $tempDir = sys_get_temp_dir();
        $file = $tempDir.'/messages.en.concrete';
        $backupFile = $file.'~';

        @touch($file);

        $catalogue = new MessageCatalogue('en');
        $catalogue->add(['foo' => 'bar']);

        $dumper = new ConcreteFileDumper();
        $dumper->dump($catalogue, ['path' => $tempDir]);

        self::assertFileExists($backupFile);

        @unlink($file);
        @unlink($backupFile);
    }

    public function testDumpCreatesNestedDirectoriesAndFile(): void
    {
        $tempDir = sys_get_temp_dir();
        $translationsDir = $tempDir.'/test/translations';
        $file = $translationsDir.'/messages.en.concrete';

        $catalogue = new MessageCatalogue('en');
        $catalogue->add(['foo' => 'bar']);

        $dumper = new ConcreteFileDumper();
        $dumper->setRelativePathTemplate('test/translations/%domain%.%locale%.%extension%');
        $dumper->dump($catalogue, ['path' => $tempDir]);

        self::assertFileExists($file);

        @unlink($file);
        @rmdir($translationsDir);
    }
}

class ConcreteFileDumper extends FileDumper
{
    public function formatCatalogue(MessageCatalogue $messages, $domain, array $options = [])
    {
        return '';
    }

    protected function getExtension()
    {
        return 'concrete';
    }
}
