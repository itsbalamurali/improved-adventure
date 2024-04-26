<?php



/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Config\Tests\Resource;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\Resource\DirectoryResource;

/**
 * @internal
 *
 * @coversNothing
 */
final class DirectoryResourceTest extends TestCase
{
    protected $directory;

    protected function setUp(): void
    {
        $this->directory = sys_get_temp_dir().\DIRECTORY_SEPARATOR.'symfonyDirectoryIterator';
        if (!file_exists($this->directory)) {
            mkdir($this->directory);
        }
        touch($this->directory.'/tmp.xml');
    }

    protected function tearDown(): void
    {
        if (!is_dir($this->directory)) {
            return;
        }
        $this->removeDirectory($this->directory);
    }

    public function testGetResource(): void
    {
        $resource = new DirectoryResource($this->directory);
        self::assertSame($this->directory, $resource->getResource(), '->getResource() returns the path to the resource');
    }

    public function testGetPattern(): void
    {
        $resource = new DirectoryResource('foo', 'bar');
        self::assertSame('bar', $resource->getPattern());
    }

    public function testIsFresh(): void
    {
        $resource = new DirectoryResource($this->directory);
        self::assertTrue($resource->isFresh(time() + 10), '->isFresh() returns true if the resource has not changed');
        self::assertFalse($resource->isFresh(time() - 86_400), '->isFresh() returns false if the resource has been updated');

        $resource = new DirectoryResource('/____foo/foobar'.random_int(1, 999_999));
        self::assertFalse($resource->isFresh(time()), '->isFresh() returns false if the resource does not exist');
    }

    public function testIsFreshUpdateFile(): void
    {
        $resource = new DirectoryResource($this->directory);
        touch($this->directory.'/tmp.xml', time() + 20);
        self::assertFalse($resource->isFresh(time() + 10), '->isFresh() returns false if an existing file is modified');
    }

    public function testIsFreshNewFile(): void
    {
        $resource = new DirectoryResource($this->directory);
        touch($this->directory.'/new.xml', time() + 20);
        self::assertFalse($resource->isFresh(time() + 10), '->isFresh() returns false if a new file is added');
    }

    public function testIsFreshNewFileWithDifferentPattern(): void
    {
        $resource = new DirectoryResource($this->directory, '/.xml$/');
        touch($this->directory.'/new.yaml', time() + 20);
        self::assertTrue($resource->isFresh(time() + 10), '->isFresh() returns true if a new file with a non-matching pattern is added');
    }

    public function testIsFreshDeleteFile(): void
    {
        $resource = new DirectoryResource($this->directory);
        $time = time();
        sleep(1);
        unlink($this->directory.'/tmp.xml');
        self::assertFalse($resource->isFresh($time), '->isFresh() returns false if an existing file is removed');
    }

    public function testIsFreshDeleteDirectory(): void
    {
        $resource = new DirectoryResource($this->directory);
        $this->removeDirectory($this->directory);
        self::assertFalse($resource->isFresh(time()), '->isFresh() returns false if the whole resource is removed');
    }

    public function testIsFreshCreateFileInSubdirectory(): void
    {
        $subdirectory = $this->directory.'/subdirectory';
        mkdir($subdirectory);

        $resource = new DirectoryResource($this->directory);
        self::assertTrue($resource->isFresh(time() + 10), '->isFresh() returns true if an unmodified subdirectory exists');

        touch($subdirectory.'/newfile.xml', time() + 20);
        self::assertFalse($resource->isFresh(time() + 10), '->isFresh() returns false if a new file in a subdirectory is added');
    }

    public function testIsFreshModifySubdirectory(): void
    {
        $resource = new DirectoryResource($this->directory);

        $subdirectory = $this->directory.'/subdirectory';
        mkdir($subdirectory);
        touch($subdirectory, time() + 20);

        self::assertFalse($resource->isFresh(time() + 10), '->isFresh() returns false if a subdirectory is modified (e.g. a file gets deleted)');
    }

    public function testFilterRegexListNoMatch(): void
    {
        $resource = new DirectoryResource($this->directory, '/\.(foo|xml)$/');

        touch($this->directory.'/new.bar', time() + 20);
        self::assertTrue($resource->isFresh(time() + 10), '->isFresh() returns true if a new file not matching the filter regex is created');
    }

    public function testFilterRegexListMatch(): void
    {
        $resource = new DirectoryResource($this->directory, '/\.(foo|xml)$/');

        touch($this->directory.'/new.xml', time() + 20);
        self::assertFalse($resource->isFresh(time() + 10), '->isFresh() returns false if an new file matching the filter regex is created ');
    }

    public function testSerializeUnserialize(): void
    {
        $resource = new DirectoryResource($this->directory, '/\.(foo|xml)$/');

        $unserialized = unserialize(serialize($resource));

        self::assertSame($this->directory, $resource->getResource());
        self::assertSame('/\.(foo|xml)$/', $resource->getPattern());
    }

    public function testResourcesWithDifferentPatternsAreDifferent(): void
    {
        $resourceA = new DirectoryResource($this->directory, '/.xml$/');
        $resourceB = new DirectoryResource($this->directory, '/.yaml$/');

        self::assertCount(2, array_unique([$resourceA, $resourceB]));
    }

    protected function removeDirectory($directory): void
    {
        $iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($directory), \RecursiveIteratorIterator::CHILD_FIRST);
        foreach ($iterator as $path) {
            if (preg_match('#[/\\\\]\.\.?$#', $path->__toString())) {
                continue;
            }
            if ($path->isDir()) {
                rmdir($path->__toString());
            } else {
                unlink($path->__toString());
            }
        }
        rmdir($directory);
    }
}
