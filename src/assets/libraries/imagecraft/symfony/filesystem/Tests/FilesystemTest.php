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

/**
 * Test class for Filesystem.
 *
 * @internal
 *
 * @coversNothing
 */
final class FilesystemTest extends FilesystemTestCase
{
    public function testCopyCreatesNewFile(): void
    {
        $sourceFilePath = $this->workspace.\DIRECTORY_SEPARATOR.'copy_source_file';
        $targetFilePath = $this->workspace.\DIRECTORY_SEPARATOR.'copy_target_file';

        file_put_contents($sourceFilePath, 'SOURCE FILE');

        $this->filesystem->copy($sourceFilePath, $targetFilePath);

        self::assertFileExists($targetFilePath);
        self::assertSame('SOURCE FILE', file_get_contents($targetFilePath));
    }

    public function testCopyFails(): void
    {
        $this->expectException(IOException::class);

        $sourceFilePath = $this->workspace.\DIRECTORY_SEPARATOR.'copy_source_file';
        $targetFilePath = $this->workspace.\DIRECTORY_SEPARATOR.'copy_target_file';

        $this->filesystem->copy($sourceFilePath, $targetFilePath);
    }

    public function testCopyUnreadableFileFails(): void
    {
        $this->expectException(IOException::class);

        // skip test on Windows; PHP can't easily set file as unreadable on Windows
        if ('\\' === \DIRECTORY_SEPARATOR) {
            self::markTestSkipped('This test cannot run on Windows.');
        }

        $sourceFilePath = $this->workspace.\DIRECTORY_SEPARATOR.'copy_source_file';
        $targetFilePath = $this->workspace.\DIRECTORY_SEPARATOR.'copy_target_file';

        file_put_contents($sourceFilePath, 'SOURCE FILE');

        // make sure target cannot be read
        $this->filesystem->chmod($sourceFilePath, 0222);

        $this->filesystem->copy($sourceFilePath, $targetFilePath);
    }

    public function testCopyOverridesExistingFileIfModified(): void
    {
        $sourceFilePath = $this->workspace.\DIRECTORY_SEPARATOR.'copy_source_file';
        $targetFilePath = $this->workspace.\DIRECTORY_SEPARATOR.'copy_target_file';

        file_put_contents($sourceFilePath, 'SOURCE FILE');
        file_put_contents($targetFilePath, 'TARGET FILE');
        touch($targetFilePath, time() - 1_000);

        $this->filesystem->copy($sourceFilePath, $targetFilePath);

        self::assertFileExists($targetFilePath);
        self::assertSame('SOURCE FILE', file_get_contents($targetFilePath));
    }

    public function testCopyDoesNotOverrideExistingFileByDefault(): void
    {
        $sourceFilePath = $this->workspace.\DIRECTORY_SEPARATOR.'copy_source_file';
        $targetFilePath = $this->workspace.\DIRECTORY_SEPARATOR.'copy_target_file';

        file_put_contents($sourceFilePath, 'SOURCE FILE');
        file_put_contents($targetFilePath, 'TARGET FILE');

        // make sure both files have the same modification time
        $modificationTime = time() - 1_000;
        touch($sourceFilePath, $modificationTime);
        touch($targetFilePath, $modificationTime);

        $this->filesystem->copy($sourceFilePath, $targetFilePath);

        self::assertFileExists($targetFilePath);
        self::assertSame('TARGET FILE', file_get_contents($targetFilePath));
    }

    public function testCopyOverridesExistingFileIfForced(): void
    {
        $sourceFilePath = $this->workspace.\DIRECTORY_SEPARATOR.'copy_source_file';
        $targetFilePath = $this->workspace.\DIRECTORY_SEPARATOR.'copy_target_file';

        file_put_contents($sourceFilePath, 'SOURCE FILE');
        file_put_contents($targetFilePath, 'TARGET FILE');

        // make sure both files have the same modification time
        $modificationTime = time() - 1_000;
        touch($sourceFilePath, $modificationTime);
        touch($targetFilePath, $modificationTime);

        $this->filesystem->copy($sourceFilePath, $targetFilePath, true);

        self::assertFileExists($targetFilePath);
        self::assertSame('SOURCE FILE', file_get_contents($targetFilePath));
    }

    public function testCopyWithOverrideWithReadOnlyTargetFails(): void
    {
        $this->expectException(IOException::class);

        // skip test on Windows; PHP can't easily set file as unwritable on Windows
        if ('\\' === \DIRECTORY_SEPARATOR) {
            self::markTestSkipped('This test cannot run on Windows.');
        }

        $sourceFilePath = $this->workspace.\DIRECTORY_SEPARATOR.'copy_source_file';
        $targetFilePath = $this->workspace.\DIRECTORY_SEPARATOR.'copy_target_file';

        file_put_contents($sourceFilePath, 'SOURCE FILE');
        file_put_contents($targetFilePath, 'TARGET FILE');

        // make sure both files have the same modification time
        $modificationTime = time() - 1_000;
        touch($sourceFilePath, $modificationTime);
        touch($targetFilePath, $modificationTime);

        // make sure target is read-only
        $this->filesystem->chmod($targetFilePath, 0444);

        $this->filesystem->copy($sourceFilePath, $targetFilePath, true);
    }

    public function testCopyCreatesTargetDirectoryIfItDoesNotExist(): void
    {
        $sourceFilePath = $this->workspace.\DIRECTORY_SEPARATOR.'copy_source_file';
        $targetFileDirectory = $this->workspace.\DIRECTORY_SEPARATOR.'directory';
        $targetFilePath = $targetFileDirectory.\DIRECTORY_SEPARATOR.'copy_target_file';

        file_put_contents($sourceFilePath, 'SOURCE FILE');

        $this->filesystem->copy($sourceFilePath, $targetFilePath);

        self::assertDirectoryExists($targetFileDirectory);
        self::assertFileExists($targetFilePath);
        self::assertSame('SOURCE FILE', file_get_contents($targetFilePath));
    }

    public function testCopyForOriginUrlsAndExistingLocalFileDefaultsToCopy(): void
    {
        $sourceFilePath = 'http://symfony.com/images/common/logo/logo_symfony_header.png';
        $targetFilePath = $this->workspace.\DIRECTORY_SEPARATOR.'copy_target_file';

        file_put_contents($targetFilePath, 'TARGET FILE');

        $this->filesystem->copy($sourceFilePath, $targetFilePath, false);

        self::assertFileExists($targetFilePath);
        self::assertSame(file_get_contents($sourceFilePath), file_get_contents($targetFilePath));
    }

    public function testMkdirCreatesDirectoriesRecursively(): void
    {
        $directory = $this->workspace
            .\DIRECTORY_SEPARATOR.'directory'
            .\DIRECTORY_SEPARATOR.'sub_directory';

        $this->filesystem->mkdir($directory);

        self::assertDirectoryExists($directory);
    }

    public function testMkdirCreatesDirectoriesFromArray(): void
    {
        $basePath = $this->workspace.\DIRECTORY_SEPARATOR;
        $directories = [
            $basePath.'1', $basePath.'2', $basePath.'3',
        ];

        $this->filesystem->mkdir($directories);

        self::assertDirectoryExists($basePath.'1');
        self::assertDirectoryExists($basePath.'2');
        self::assertDirectoryExists($basePath.'3');
    }

    public function testMkdirCreatesDirectoriesFromTraversableObject(): void
    {
        $basePath = $this->workspace.\DIRECTORY_SEPARATOR;
        $directories = new \ArrayObject([
            $basePath.'1', $basePath.'2', $basePath.'3',
        ]);

        $this->filesystem->mkdir($directories);

        self::assertDirectoryExists($basePath.'1');
        self::assertDirectoryExists($basePath.'2');
        self::assertDirectoryExists($basePath.'3');
    }

    public function testMkdirCreatesDirectoriesFails(): void
    {
        $this->expectException(IOException::class);

        $basePath = $this->workspace.\DIRECTORY_SEPARATOR;
        $dir = $basePath.'2';

        file_put_contents($dir, '');

        $this->filesystem->mkdir($dir);
    }

    public function testTouchCreatesEmptyFile(): void
    {
        $file = $this->workspace.\DIRECTORY_SEPARATOR.'1';

        $this->filesystem->touch($file);

        self::assertFileExists($file);
    }

    public function testTouchFails(): void
    {
        $this->expectException(IOException::class);

        $file = $this->workspace.\DIRECTORY_SEPARATOR.'1'.\DIRECTORY_SEPARATOR.'2';

        $this->filesystem->touch($file);
    }

    public function testTouchCreatesEmptyFilesFromArray(): void
    {
        $basePath = $this->workspace.\DIRECTORY_SEPARATOR;
        $files = [
            $basePath.'1', $basePath.'2', $basePath.'3',
        ];

        $this->filesystem->touch($files);

        self::assertFileExists($basePath.'1');
        self::assertFileExists($basePath.'2');
        self::assertFileExists($basePath.'3');
    }

    public function testTouchCreatesEmptyFilesFromTraversableObject(): void
    {
        $basePath = $this->workspace.\DIRECTORY_SEPARATOR;
        $files = new \ArrayObject([
            $basePath.'1', $basePath.'2', $basePath.'3',
        ]);

        $this->filesystem->touch($files);

        self::assertFileExists($basePath.'1');
        self::assertFileExists($basePath.'2');
        self::assertFileExists($basePath.'3');
    }

    public function testRemoveCleansFilesAndDirectoriesIteratively(): void
    {
        $basePath = $this->workspace.\DIRECTORY_SEPARATOR.'directory'.\DIRECTORY_SEPARATOR;

        mkdir($basePath);
        mkdir($basePath.'dir');
        touch($basePath.'file');

        $this->filesystem->remove($basePath);

        self::assertFileNotExists($basePath);
    }

    public function testRemoveCleansArrayOfFilesAndDirectories(): void
    {
        $basePath = $this->workspace.\DIRECTORY_SEPARATOR;

        mkdir($basePath.'dir');
        touch($basePath.'file');

        $files = [
            $basePath.'dir', $basePath.'file',
        ];

        $this->filesystem->remove($files);

        self::assertFileNotExists($basePath.'dir');
        self::assertFileNotExists($basePath.'file');
    }

    public function testRemoveCleansTraversableObjectOfFilesAndDirectories(): void
    {
        $basePath = $this->workspace.\DIRECTORY_SEPARATOR;

        mkdir($basePath.'dir');
        touch($basePath.'file');

        $files = new \ArrayObject([
            $basePath.'dir', $basePath.'file',
        ]);

        $this->filesystem->remove($files);

        self::assertFileNotExists($basePath.'dir');
        self::assertFileNotExists($basePath.'file');
    }

    public function testRemoveIgnoresNonExistingFiles(): void
    {
        $basePath = $this->workspace.\DIRECTORY_SEPARATOR;

        mkdir($basePath.'dir');

        $files = [
            $basePath.'dir', $basePath.'file',
        ];

        $this->filesystem->remove($files);

        self::assertFileNotExists($basePath.'dir');
    }

    public function testRemoveCleansInvalidLinks(): void
    {
        $this->markAsSkippedIfSymlinkIsMissing();

        $basePath = $this->workspace.\DIRECTORY_SEPARATOR.'directory'.\DIRECTORY_SEPARATOR;

        mkdir($basePath);
        mkdir($basePath.'dir');
        // create symlink to nonexistent file
        @symlink($basePath.'file', $basePath.'file-link');

        // create symlink to dir using trailing forward slash
        $this->filesystem->symlink($basePath.'dir/', $basePath.'dir-link');
        self::assertDirectoryExists($basePath.'dir-link');

        // create symlink to nonexistent dir
        rmdir($basePath.'dir');
        self::assertFalse('\\' === \DIRECTORY_SEPARATOR ? @readlink($basePath.'dir-link') : is_dir($basePath.'dir-link'));

        $this->filesystem->remove($basePath);

        self::assertFileNotExists($basePath);
    }

    public function testFilesExists(): void
    {
        $basePath = $this->workspace.\DIRECTORY_SEPARATOR.'directory'.\DIRECTORY_SEPARATOR;

        mkdir($basePath);
        touch($basePath.'file1');
        mkdir($basePath.'folder');

        self::assertTrue($this->filesystem->exists($basePath.'file1'));
        self::assertTrue($this->filesystem->exists($basePath.'folder'));
    }

    public function testFilesExistsFails(): void
    {
        $this->expectException(IOException::class);

        if ('\\' !== \DIRECTORY_SEPARATOR) {
            self::markTestSkipped('Test covers edge case on Windows only.');
        }

        $basePath = $this->workspace.'\\directory\\';

        $oldPath = getcwd();
        mkdir($basePath);
        chdir($basePath);
        $file = str_repeat('T', 259 - \strlen($basePath));
        $path = $basePath.$file;
        exec('TYPE NUL >>'.$file); // equivalent of touch, we can not use the php touch() here because it suffers from the same limitation
        $this->longPathNamesWindows[] = $path; // save this so we can clean up later
        chdir($oldPath);
        $this->filesystem->exists($path);
    }

    public function testFilesExistsTraversableObjectOfFilesAndDirectories(): void
    {
        $basePath = $this->workspace.\DIRECTORY_SEPARATOR;

        mkdir($basePath.'dir');
        touch($basePath.'file');

        $files = new \ArrayObject([
            $basePath.'dir', $basePath.'file',
        ]);

        self::assertTrue($this->filesystem->exists($files));
    }

    public function testFilesNotExistsTraversableObjectOfFilesAndDirectories(): void
    {
        $basePath = $this->workspace.\DIRECTORY_SEPARATOR;

        mkdir($basePath.'dir');
        touch($basePath.'file');
        touch($basePath.'file2');

        $files = new \ArrayObject([
            $basePath.'dir', $basePath.'file', $basePath.'file2',
        ]);

        unlink($basePath.'file');

        self::assertFalse($this->filesystem->exists($files));
    }

    public function testInvalidFileNotExists(): void
    {
        $basePath = $this->workspace.\DIRECTORY_SEPARATOR.'directory'.\DIRECTORY_SEPARATOR;

        self::assertFalse($this->filesystem->exists($basePath.time()));
    }

    public function testChmodChangesFileMode(): void
    {
        $this->markAsSkippedIfChmodIsMissing();

        $dir = $this->workspace.\DIRECTORY_SEPARATOR.'dir';
        mkdir($dir);
        $file = $dir.\DIRECTORY_SEPARATOR.'file';
        touch($file);

        $this->filesystem->chmod($file, 0400);
        $this->filesystem->chmod($dir, 0753);

        $this->assertFilePermissions(753, $dir);
        $this->assertFilePermissions(400, $file);
    }

    public function testChmodWrongMod(): void
    {
        $this->markAsSkippedIfChmodIsMissing();

        $dir = $this->workspace.\DIRECTORY_SEPARATOR.'file';
        touch($dir);

        $this->filesystem->chmod($dir, 'Wrongmode');
    }

    public function testChmodRecursive(): void
    {
        $this->markAsSkippedIfChmodIsMissing();

        $dir = $this->workspace.\DIRECTORY_SEPARATOR.'dir';
        mkdir($dir);
        $file = $dir.\DIRECTORY_SEPARATOR.'file';
        touch($file);

        $this->filesystem->chmod($file, 0400, 0000, true);
        $this->filesystem->chmod($dir, 0753, 0000, true);

        $this->assertFilePermissions(753, $dir);
        $this->assertFilePermissions(753, $file);
    }

    public function testChmodAppliesUmask(): void
    {
        $this->markAsSkippedIfChmodIsMissing();

        $file = $this->workspace.\DIRECTORY_SEPARATOR.'file';
        touch($file);

        $this->filesystem->chmod($file, 0770, 0022);
        $this->assertFilePermissions(750, $file);
    }

    public function testChmodChangesModeOfArrayOfFiles(): void
    {
        $this->markAsSkippedIfChmodIsMissing();

        $directory = $this->workspace.\DIRECTORY_SEPARATOR.'directory';
        $file = $this->workspace.\DIRECTORY_SEPARATOR.'file';
        $files = [$directory, $file];

        mkdir($directory);
        touch($file);

        $this->filesystem->chmod($files, 0753);

        $this->assertFilePermissions(753, $file);
        $this->assertFilePermissions(753, $directory);
    }

    public function testChmodChangesModeOfTraversableFileObject(): void
    {
        $this->markAsSkippedIfChmodIsMissing();

        $directory = $this->workspace.\DIRECTORY_SEPARATOR.'directory';
        $file = $this->workspace.\DIRECTORY_SEPARATOR.'file';
        $files = new \ArrayObject([$directory, $file]);

        mkdir($directory);
        touch($file);

        $this->filesystem->chmod($files, 0753);

        $this->assertFilePermissions(753, $file);
        $this->assertFilePermissions(753, $directory);
    }

    public function testChmodChangesZeroModeOnSubdirectoriesOnRecursive(): void
    {
        $this->markAsSkippedIfChmodIsMissing();

        $directory = $this->workspace.\DIRECTORY_SEPARATOR.'directory';
        $subdirectory = $directory.\DIRECTORY_SEPARATOR.'subdirectory';

        mkdir($directory);
        mkdir($subdirectory);
        chmod($subdirectory, 0000);

        $this->filesystem->chmod($directory, 0753, 0000, true);

        $this->assertFilePermissions(753, $subdirectory);
    }

    public function testChown(): void
    {
        $this->markAsSkippedIfPosixIsMissing();

        $dir = $this->workspace.\DIRECTORY_SEPARATOR.'dir';
        mkdir($dir);

        $this->filesystem->chown($dir, $this->getFileOwner($dir));
    }

    public function testChownRecursive(): void
    {
        $this->markAsSkippedIfPosixIsMissing();

        $dir = $this->workspace.\DIRECTORY_SEPARATOR.'dir';
        mkdir($dir);
        $file = $dir.\DIRECTORY_SEPARATOR.'file';
        touch($file);

        $this->filesystem->chown($dir, $this->getFileOwner($dir), true);
    }

    public function testChownSymlink(): void
    {
        $this->markAsSkippedIfSymlinkIsMissing();

        $file = $this->workspace.\DIRECTORY_SEPARATOR.'file';
        $link = $this->workspace.\DIRECTORY_SEPARATOR.'link';

        touch($file);

        $this->filesystem->symlink($file, $link);

        $this->filesystem->chown($link, $this->getFileOwner($link));
    }

    public function testChownSymlinkFails(): void
    {
        $this->expectException(IOException::class);

        $this->markAsSkippedIfSymlinkIsMissing();

        $file = $this->workspace.\DIRECTORY_SEPARATOR.'file';
        $link = $this->workspace.\DIRECTORY_SEPARATOR.'link';

        touch($file);

        $this->filesystem->symlink($file, $link);

        $this->filesystem->chown($link, 'user'.time().random_int(1_000, 9_999));
    }

    public function testChownFail(): void
    {
        $this->expectException(IOException::class);

        $this->markAsSkippedIfPosixIsMissing();

        $dir = $this->workspace.\DIRECTORY_SEPARATOR.'dir';
        mkdir($dir);

        $this->filesystem->chown($dir, 'user'.time().random_int(1_000, 9_999));
    }

    public function testChgrp(): void
    {
        $this->markAsSkippedIfPosixIsMissing();

        $dir = $this->workspace.\DIRECTORY_SEPARATOR.'dir';
        mkdir($dir);

        $this->filesystem->chgrp($dir, $this->getFileGroup($dir));
    }

    public function testChgrpRecursive(): void
    {
        $this->markAsSkippedIfPosixIsMissing();

        $dir = $this->workspace.\DIRECTORY_SEPARATOR.'dir';
        mkdir($dir);
        $file = $dir.\DIRECTORY_SEPARATOR.'file';
        touch($file);

        $this->filesystem->chgrp($dir, $this->getFileGroup($dir), true);
    }

    public function testChgrpSymlink(): void
    {
        $this->markAsSkippedIfSymlinkIsMissing();

        $file = $this->workspace.\DIRECTORY_SEPARATOR.'file';
        $link = $this->workspace.\DIRECTORY_SEPARATOR.'link';

        touch($file);

        $this->filesystem->symlink($file, $link);

        $this->filesystem->chgrp($link, $this->getFileGroup($link));
    }

    public function testChgrpSymlinkFails(): void
    {
        $this->expectException(IOException::class);

        $this->markAsSkippedIfSymlinkIsMissing();

        $file = $this->workspace.\DIRECTORY_SEPARATOR.'file';
        $link = $this->workspace.\DIRECTORY_SEPARATOR.'link';

        touch($file);

        $this->filesystem->symlink($file, $link);

        $this->filesystem->chgrp($link, 'user'.time().random_int(1_000, 9_999));
    }

    public function testChgrpFail(): void
    {
        $this->expectException(IOException::class);

        $this->markAsSkippedIfPosixIsMissing();

        $dir = $this->workspace.\DIRECTORY_SEPARATOR.'dir';
        mkdir($dir);

        $this->filesystem->chgrp($dir, 'user'.time().random_int(1_000, 9_999));
    }

    public function testRename(): void
    {
        $file = $this->workspace.\DIRECTORY_SEPARATOR.'file';
        $newPath = $this->workspace.\DIRECTORY_SEPARATOR.'new_file';
        touch($file);

        $this->filesystem->rename($file, $newPath);

        self::assertFileNotExists($file);
        self::assertFileExists($newPath);
    }

    public function testRenameThrowsExceptionIfTargetAlreadyExists(): void
    {
        $this->expectException(IOException::class);

        $file = $this->workspace.\DIRECTORY_SEPARATOR.'file';
        $newPath = $this->workspace.\DIRECTORY_SEPARATOR.'new_file';

        touch($file);
        touch($newPath);

        $this->filesystem->rename($file, $newPath);
    }

    public function testRenameOverwritesTheTargetIfItAlreadyExists(): void
    {
        $file = $this->workspace.\DIRECTORY_SEPARATOR.'file';
        $newPath = $this->workspace.\DIRECTORY_SEPARATOR.'new_file';

        touch($file);
        touch($newPath);

        $this->filesystem->rename($file, $newPath, true);

        self::assertFileNotExists($file);
        self::assertFileExists($newPath);
    }

    public function testRenameThrowsExceptionOnError(): void
    {
        $this->expectException(IOException::class);

        $file = $this->workspace.\DIRECTORY_SEPARATOR.uniqid('fs_test_', true);
        $newPath = $this->workspace.\DIRECTORY_SEPARATOR.'new_file';

        $this->filesystem->rename($file, $newPath);
    }

    public function testSymlink(): void
    {
        if ('\\' === \DIRECTORY_SEPARATOR) {
            self::markTestSkipped('Windows does not support creating "broken" symlinks');
        }

        $file = $this->workspace.\DIRECTORY_SEPARATOR.'file';
        $link = $this->workspace.\DIRECTORY_SEPARATOR.'link';

        // $file does not exists right now: creating "broken" links is a wanted feature
        $this->filesystem->symlink($file, $link);

        self::assertTrue(is_link($link));

        // Create the linked file AFTER creating the link
        touch($file);

        self::assertSame($file, readlink($link));
    }

    /**
     * @depends testSymlink
     */
    public function testRemoveSymlink(): void
    {
        $this->markAsSkippedIfSymlinkIsMissing();

        $link = $this->workspace.\DIRECTORY_SEPARATOR.'link';

        $this->filesystem->remove($link);

        self::assertTrue(!is_link($link));
        self::assertTrue(!is_file($link));
        self::assertTrue(!is_dir($link));
    }

    public function testSymlinkIsOverwrittenIfPointsToDifferentTarget(): void
    {
        $this->markAsSkippedIfSymlinkIsMissing();

        $file = $this->workspace.\DIRECTORY_SEPARATOR.'file';
        $link = $this->workspace.\DIRECTORY_SEPARATOR.'link';

        touch($file);
        symlink($this->workspace, $link);

        $this->filesystem->symlink($file, $link);

        self::assertTrue(is_link($link));
        self::assertSame($file, readlink($link));
    }

    public function testSymlinkIsNotOverwrittenIfAlreadyCreated(): void
    {
        $this->markAsSkippedIfSymlinkIsMissing();

        $file = $this->workspace.\DIRECTORY_SEPARATOR.'file';
        $link = $this->workspace.\DIRECTORY_SEPARATOR.'link';

        touch($file);
        symlink($file, $link);

        $this->filesystem->symlink($file, $link);

        self::assertTrue(is_link($link));
        self::assertSame($file, readlink($link));
    }

    public function testSymlinkCreatesTargetDirectoryIfItDoesNotExist(): void
    {
        $this->markAsSkippedIfSymlinkIsMissing();

        $file = $this->workspace.\DIRECTORY_SEPARATOR.'file';
        $link1 = $this->workspace.\DIRECTORY_SEPARATOR.'dir'.\DIRECTORY_SEPARATOR.'link';
        $link2 = $this->workspace.\DIRECTORY_SEPARATOR.'dir'.\DIRECTORY_SEPARATOR.'subdir'.\DIRECTORY_SEPARATOR.'link';

        touch($file);

        $this->filesystem->symlink($file, $link1);
        $this->filesystem->symlink($file, $link2);

        self::assertTrue(is_link($link1));
        self::assertSame($file, readlink($link1));
        self::assertTrue(is_link($link2));
        self::assertSame($file, readlink($link2));
    }

    /**
     * @dataProvider provideMakePathRelativeCases
     *
     * @param mixed $endPath
     * @param mixed $startPath
     * @param mixed $expectedPath
     */
    public function testMakePathRelative($endPath, $startPath, $expectedPath): void
    {
        $path = $this->filesystem->makePathRelative($endPath, $startPath);

        self::assertSame($expectedPath, $path);
    }

    /**
     * @return array
     */
    public static function provideMakePathRelativeCases(): iterable
    {
        $paths = [
            ['/var/lib/symfony/src/Symfony/', '/var/lib/symfony/src/Symfony/Component', '../'],
            ['/var/lib/symfony/src/Symfony/', '/var/lib/symfony/src/Symfony/Component/', '../'],
            ['/var/lib/symfony/src/Symfony', '/var/lib/symfony/src/Symfony/Component', '../'],
            ['/var/lib/symfony/src/Symfony', '/var/lib/symfony/src/Symfony/Component/', '../'],
            ['var/lib/symfony/', 'var/lib/symfony/src/Symfony/Component', '../../../'],
            ['/usr/lib/symfony/', '/var/lib/symfony/src/Symfony/Component', '../../../../../../usr/lib/symfony/'],
            ['/var/lib/symfony/src/Symfony/', '/var/lib/symfony/', 'src/Symfony/'],
            ['/aa/bb', '/aa/bb', './'],
            ['/aa/bb', '/aa/bb/', './'],
            ['/aa/bb/', '/aa/bb', './'],
            ['/aa/bb/', '/aa/bb/', './'],
            ['/aa/bb/cc', '/aa/bb/cc/dd', '../'],
            ['/aa/bb/cc', '/aa/bb/cc/dd/', '../'],
            ['/aa/bb/cc/', '/aa/bb/cc/dd', '../'],
            ['/aa/bb/cc/', '/aa/bb/cc/dd/', '../'],
            ['/aa/bb/cc', '/aa', 'bb/cc/'],
            ['/aa/bb/cc', '/aa/', 'bb/cc/'],
            ['/aa/bb/cc/', '/aa', 'bb/cc/'],
            ['/aa/bb/cc/', '/aa/', 'bb/cc/'],
            ['/a/aab/bb', '/a/aa', '../aab/bb/'],
            ['/a/aab/bb', '/a/aa/', '../aab/bb/'],
            ['/a/aab/bb/', '/a/aa', '../aab/bb/'],
            ['/a/aab/bb/', '/a/aa/', '../aab/bb/'],
            ['/a/aab/bb/', '/', 'a/aab/bb/'],
            ['/a/aab/bb/', '/b/aab', '../../a/aab/bb/'],
        ];

        if ('\\' === \DIRECTORY_SEPARATOR) {
            $paths[] = ['c:\var\lib/symfony/src/Symfony/', 'c:/var/lib/symfony/', 'src/Symfony/'];
        }

        return $paths;
    }

    public function testMirrorCopiesFilesAndDirectoriesRecursively(): void
    {
        $sourcePath = $this->workspace.\DIRECTORY_SEPARATOR.'source'.\DIRECTORY_SEPARATOR;
        $directory = $sourcePath.'directory'.\DIRECTORY_SEPARATOR;
        $file1 = $directory.'file1';
        $file2 = $sourcePath.'file2';

        mkdir($sourcePath);
        mkdir($directory);
        file_put_contents($file1, 'FILE1');
        file_put_contents($file2, 'FILE2');

        $targetPath = $this->workspace.\DIRECTORY_SEPARATOR.'target'.\DIRECTORY_SEPARATOR;

        $this->filesystem->mirror($sourcePath, $targetPath);

        self::assertDirectoryExists($targetPath);
        self::assertDirectoryExists($targetPath.'directory');
        self::assertFileEquals($file1, $targetPath.'directory'.\DIRECTORY_SEPARATOR.'file1');
        self::assertFileEquals($file2, $targetPath.'file2');

        $this->filesystem->remove($file1);

        $this->filesystem->mirror($sourcePath, $targetPath, null, ['delete' => false]);
        self::assertTrue($this->filesystem->exists($targetPath.'directory'.\DIRECTORY_SEPARATOR.'file1'));

        $this->filesystem->mirror($sourcePath, $targetPath, null, ['delete' => true]);
        self::assertFalse($this->filesystem->exists($targetPath.'directory'.\DIRECTORY_SEPARATOR.'file1'));

        file_put_contents($file1, 'FILE1');

        $this->filesystem->mirror($sourcePath, $targetPath, null, ['delete' => true]);
        self::assertTrue($this->filesystem->exists($targetPath.'directory'.\DIRECTORY_SEPARATOR.'file1'));

        $this->filesystem->remove($directory);
        $this->filesystem->mirror($sourcePath, $targetPath, null, ['delete' => true]);
        self::assertFalse($this->filesystem->exists($targetPath.'directory'));
        self::assertFalse($this->filesystem->exists($targetPath.'directory'.\DIRECTORY_SEPARATOR.'file1'));
    }

    public function testMirrorCreatesEmptyDirectory(): void
    {
        $sourcePath = $this->workspace.\DIRECTORY_SEPARATOR.'source'.\DIRECTORY_SEPARATOR;

        mkdir($sourcePath);

        $targetPath = $this->workspace.\DIRECTORY_SEPARATOR.'target'.\DIRECTORY_SEPARATOR;

        $this->filesystem->mirror($sourcePath, $targetPath);

        self::assertDirectoryExists($targetPath);

        $this->filesystem->remove($sourcePath);
    }

    public function testMirrorCopiesLinks(): void
    {
        $this->markAsSkippedIfSymlinkIsMissing();

        $sourcePath = $this->workspace.\DIRECTORY_SEPARATOR.'source'.\DIRECTORY_SEPARATOR;

        mkdir($sourcePath);
        file_put_contents($sourcePath.'file1', 'FILE1');
        symlink($sourcePath.'file1', $sourcePath.'link1');

        $targetPath = $this->workspace.\DIRECTORY_SEPARATOR.'target'.\DIRECTORY_SEPARATOR;

        $this->filesystem->mirror($sourcePath, $targetPath);

        self::assertDirectoryExists($targetPath);
        self::assertFileEquals($sourcePath.'file1', $targetPath.'link1');
        self::assertTrue(is_link($targetPath.\DIRECTORY_SEPARATOR.'link1'));
    }

    public function testMirrorCopiesLinkedDirectoryContents(): void
    {
        $this->markAsSkippedIfSymlinkIsMissing(true);

        $sourcePath = $this->workspace.\DIRECTORY_SEPARATOR.'source'.\DIRECTORY_SEPARATOR;

        mkdir($sourcePath.'nested/', 0777, true);
        file_put_contents($sourcePath.'/nested/file1.txt', 'FILE1');
        // Note: We symlink directory, not file
        symlink($sourcePath.'nested', $sourcePath.'link1');

        $targetPath = $this->workspace.\DIRECTORY_SEPARATOR.'target'.\DIRECTORY_SEPARATOR;

        $this->filesystem->mirror($sourcePath, $targetPath);

        self::assertDirectoryExists($targetPath);
        self::assertFileEquals($sourcePath.'/nested/file1.txt', $targetPath.'link1/file1.txt');
        self::assertTrue(is_link($targetPath.\DIRECTORY_SEPARATOR.'link1'));
    }

    public function testMirrorCopiesRelativeLinkedContents(): void
    {
        $this->markAsSkippedIfSymlinkIsMissing(true);

        $sourcePath = $this->workspace.\DIRECTORY_SEPARATOR.'source'.\DIRECTORY_SEPARATOR;
        $oldPath = getcwd();

        mkdir($sourcePath.'nested/', 0777, true);
        file_put_contents($sourcePath.'/nested/file1.txt', 'FILE1');
        // Note: Create relative symlink
        chdir($sourcePath);
        symlink('nested', 'link1');

        chdir($oldPath);

        $targetPath = $this->workspace.\DIRECTORY_SEPARATOR.'target'.\DIRECTORY_SEPARATOR;

        $this->filesystem->mirror($sourcePath, $targetPath);

        self::assertDirectoryExists($targetPath);
        self::assertFileEquals($sourcePath.'/nested/file1.txt', $targetPath.'link1/file1.txt');
        self::assertTrue(is_link($targetPath.\DIRECTORY_SEPARATOR.'link1'));
        self::assertSame('\\' === \DIRECTORY_SEPARATOR ? realpath($sourcePath.'\nested') : 'nested', readlink($targetPath.\DIRECTORY_SEPARATOR.'link1'));
    }

    /**
     * @dataProvider provideIsAbsolutePathCases
     *
     * @param mixed $path
     * @param mixed $expectedResult
     */
    public function testIsAbsolutePath($path, $expectedResult): void
    {
        $result = $this->filesystem->isAbsolutePath($path);

        self::assertSame($expectedResult, $result);
    }

    /**
     * @return array
     */
    public static function provideIsAbsolutePathCases(): iterable
    {
        return [
            ['/var/lib', true],
            ['c:\\\\var\\lib', true],
            ['\\var\\lib', true],
            ['var/lib', false],
            ['../var/lib', false],
            ['', false],
            [null, false],
        ];
    }

    public function testTempnam(): void
    {
        $dirname = $this->workspace;

        $filename = $this->filesystem->tempnam($dirname, 'foo');

        self::assertFileExists($filename);
    }

    public function testTempnamWithFileScheme(): void
    {
        $scheme = 'file://';
        $dirname = $scheme.$this->workspace;

        $filename = $this->filesystem->tempnam($dirname, 'foo');

        self::assertStringStartsWith($scheme, $filename);
        self::assertFileExists($filename);
    }

    public function testTempnamWithMockScheme(): void
    {
        stream_wrapper_register('mock', 'Symfony\Component\Filesystem\Tests\Fixtures\MockStream\MockStream');

        $scheme = 'mock://';
        $dirname = $scheme.$this->workspace;

        $filename = $this->filesystem->tempnam($dirname, 'foo');

        self::assertStringStartsWith($scheme, $filename);
        self::assertFileExists($filename);
    }

    public function testTempnamWithZlibSchemeFails(): void
    {
        $this->expectException(IOException::class);

        $scheme = 'compress.zlib://';
        $dirname = $scheme.$this->workspace;

        // The compress.zlib:// stream does not support mode x: creates the file, errors "failed to open stream: operation failed" and returns false
        $this->filesystem->tempnam($dirname, 'bar');
    }

    public function testTempnamWithPHPTempSchemeFails(): void
    {
        $scheme = 'php://temp';
        $dirname = $scheme;

        $filename = $this->filesystem->tempnam($dirname, 'bar');

        self::assertStringStartsWith($scheme, $filename);

        // The php://temp stream deletes the file after close
        self::assertFileNotExists($filename);
    }

    public function testTempnamWithPharSchemeFails(): void
    {
        $this->expectException(IOException::class);

        // Skip test if Phar disabled phar.readonly must be 0 in php.ini
        if (!\Phar::canWrite()) {
            self::markTestSkipped('This test cannot run when phar.readonly is 1.');
        }

        $scheme = 'phar://';
        $dirname = $scheme.$this->workspace;
        $pharname = 'foo.phar';

        new \Phar($this->workspace.'/'.$pharname, 0, $pharname);
        // The phar:// stream does not support mode x: fails to create file, errors "failed to open stream: phar error: "$filename" is not a file in phar "$pharname"" and returns false
        $this->filesystem->tempnam($dirname, $pharname.'/bar');
    }

    public function testTempnamWithHTTPSchemeFails(): void
    {
        $this->expectException(IOException::class);

        $scheme = 'http://';
        $dirname = $scheme.$this->workspace;

        // The http:// scheme is read-only
        $this->filesystem->tempnam($dirname, 'bar');
    }

    public function testTempnamOnUnwritableFallsBackToSysTmp(): void
    {
        $scheme = 'file://';
        $dirname = $scheme.$this->workspace.\DIRECTORY_SEPARATOR.'does_not_exist';

        $filename = $this->filesystem->tempnam($dirname, 'bar');
        $realTempDir = realpath(sys_get_temp_dir());
        self::assertStringStartsWith(rtrim($scheme.$realTempDir, \DIRECTORY_SEPARATOR), $filename);
        self::assertFileExists($filename);

        // Tear down
        @unlink($filename);
    }

    public function testDumpFile(): void
    {
        $filename = $this->workspace.\DIRECTORY_SEPARATOR.'foo'.\DIRECTORY_SEPARATOR.'baz.txt';

        $this->filesystem->dumpFile($filename, 'bar');
        self::assertFileExists($filename);
        self::assertSame('bar', file_get_contents($filename));

        // skip mode check on Windows
        if ('\\' !== \DIRECTORY_SEPARATOR) {
            $this->assertFilePermissions(666, $filename);
        }
    }

    public function testDumpFileOverwritesAnExistingFile(): void
    {
        $filename = $this->workspace.\DIRECTORY_SEPARATOR.'foo.txt';
        file_put_contents($filename, 'FOO BAR');

        $this->filesystem->dumpFile($filename, 'bar');

        self::assertFileExists($filename);
        self::assertSame('bar', file_get_contents($filename));
    }

    public function testDumpFileWithFileScheme(): void
    {
        if (\defined('HHVM_VERSION')) {
            self::markTestSkipped('HHVM does not handle the file:// scheme correctly');
        }

        $scheme = 'file://';
        $filename = $scheme.$this->workspace.\DIRECTORY_SEPARATOR.'foo'.\DIRECTORY_SEPARATOR.'baz.txt';

        $this->filesystem->dumpFile($filename, 'bar', null);

        self::assertFileExists($filename);
        self::assertSame('bar', file_get_contents($filename));
    }

    public function testDumpFileWithZlibScheme(): void
    {
        $scheme = 'compress.zlib://';
        $filename = $this->workspace.\DIRECTORY_SEPARATOR.'foo'.\DIRECTORY_SEPARATOR.'baz.txt';

        $this->filesystem->dumpFile($filename, 'bar', null);

        // Zlib stat uses file:// wrapper so remove scheme
        self::assertFileExists(str_replace($scheme, '', $filename));
        self::assertSame('bar', file_get_contents($filename));
    }

    public function testCopyShouldKeepExecutionPermission(): void
    {
        $this->markAsSkippedIfChmodIsMissing();

        $sourceFilePath = $this->workspace.\DIRECTORY_SEPARATOR.'copy_source_file';
        $targetFilePath = $this->workspace.\DIRECTORY_SEPARATOR.'copy_target_file';

        file_put_contents($sourceFilePath, 'SOURCE FILE');
        chmod($sourceFilePath, 0745);

        $this->filesystem->copy($sourceFilePath, $targetFilePath);

        $this->assertFilePermissions(767, $targetFilePath);
    }
}
