<?php



/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Translation\Tests\Writer;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Translation\Dumper\DumperInterface;
use Symfony\Component\Translation\MessageCatalogue;
use Symfony\Component\Translation\Writer\TranslationWriter;

/**
 * @internal
 *
 * @coversNothing
 */
final class TranslationWriterTest extends TestCase
{
    public function testWriteTranslations(): void
    {
        $dumper = $this->getMockBuilder('Symfony\Component\Translation\Dumper\DumperInterface')->getMock();
        $dumper
            ->expects(self::once())
            ->method('dump')
        ;

        $writer = new TranslationWriter();
        $writer->addDumper('test', $dumper);
        $writer->writeTranslations(new MessageCatalogue('en'), 'test');
    }

    public function testDisableBackup(): void
    {
        $nonBackupDumper = new NonBackupDumper();
        $backupDumper = new BackupDumper();

        $writer = new TranslationWriter();
        $writer->addDumper('non_backup', $nonBackupDumper);
        $writer->addDumper('backup', $backupDumper);
        $writer->disableBackup();

        self::assertFalse($backupDumper->backup, 'backup can be disabled if setBackup() method does exist');
    }
}

class NonBackupDumper implements DumperInterface
{
    public function dump(MessageCatalogue $messages, $options = []): void {}
}

class BackupDumper implements DumperInterface
{
    public $backup = true;

    public function dump(MessageCatalogue $messages, $options = []): void {}

    public function setBackup($backup): void
    {
        $this->backup = $backup;
    }
}
