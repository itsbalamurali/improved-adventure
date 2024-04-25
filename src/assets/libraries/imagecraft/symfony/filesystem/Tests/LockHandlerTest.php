<?php



/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Filesystem\Tests;

use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Filesystem\LockHandler;

/**
 * @internal
 *
 * @coversNothing
 */
final class LockHandlerTest extends \PHPUnit\Framework\TestCase
{
    public function testConstructWhenRepositoryDoesNotExist(): void
    {
        $this->expectException(IOException::class);
        $this->expectExceptionMessage('Failed to create "/a/b/c/d/e": mkdir(): Permission denied.');

        if (!getenv('USER') || 'root' === getenv('USER')) {
            self::markTestSkipped('This test will fail if run under superuser');
        }
        new LockHandler('lock', '/a/b/c/d/e');
    }

    public function testConstructWhenRepositoryIsNotWriteable(): void
    {
        $this->expectException(IOException::class);
        $this->expectExceptionMessage('The directory "/" is not writable.');

        if (!getenv('USER') || 'root' === getenv('USER')) {
            self::markTestSkipped('This test will fail if run under superuser');
        }
        new LockHandler('lock', '/');
    }

    public function testConstructSanitizeName(): void
    {
        $lock = new LockHandler('<?php echo "% hello word ! %" ?>');

        $file = sprintf('%s/sf.-php-echo-hello-word-.4b3d9d0d27ddef3a78a64685dda3a963e478659a9e5240feaf7b4173a8f28d5f.lock', sys_get_temp_dir());
        // ensure the file does not exist before the lock
        @unlink($file);

        $lock->lock();

        self::assertFileExists($file);

        $lock->release();
    }

    public function testLockRelease(): void
    {
        $name = 'symfony-test-filesystem.lock';

        $l1 = new LockHandler($name);
        $l2 = new LockHandler($name);

        self::assertTrue($l1->lock());
        self::assertFalse($l2->lock());

        $l1->release();

        self::assertTrue($l2->lock());
        $l2->release();
    }

    public function testLockTwice(): void
    {
        $name = 'symfony-test-filesystem.lock';

        $lockHandler = new LockHandler($name);

        self::assertTrue($lockHandler->lock());
        self::assertTrue($lockHandler->lock());

        $lockHandler->release();
    }

    public function testLockIsReleased(): void
    {
        $name = 'symfony-test-filesystem.lock';

        $l1 = new LockHandler($name);
        $l2 = new LockHandler($name);

        self::assertTrue($l1->lock());
        self::assertFalse($l2->lock());

        $l1 = null;

        self::assertTrue($l2->lock());
        $l2->release();
    }
}
