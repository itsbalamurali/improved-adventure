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
use Symfony\Component\Config\Resource\FileResource;

/**
 * @internal
 *
 * @coversNothing
 */
final class FileResourceTest extends TestCase
{
    protected $resource;
    protected $file;
    protected $time;

    protected function setUp(): void
    {
        $this->file = realpath(sys_get_temp_dir()).'/tmp.xml';
        $this->time = time();
        touch($this->file, $this->time);
        $this->resource = new FileResource($this->file);
    }

    protected function tearDown(): void
    {
        unlink($this->file);
    }

    public function testGetResource(): void
    {
        self::assertSame(realpath($this->file), $this->resource->getResource(), '->getResource() returns the path to the resource');
    }

    public function testToString(): void
    {
        self::assertSame(realpath($this->file), (string) $this->resource);
    }

    public function testIsFresh(): void
    {
        self::assertTrue($this->resource->isFresh($this->time), '->isFresh() returns true if the resource has not changed in same second');
        self::assertTrue($this->resource->isFresh($this->time + 10), '->isFresh() returns true if the resource has not changed');
        self::assertFalse($this->resource->isFresh($this->time - 86_400), '->isFresh() returns false if the resource has been updated');

        $resource = new FileResource('/____foo/foobar'.random_int(1, 999_999));
        self::assertFalse($resource->isFresh($this->time), '->isFresh() returns false if the resource does not exist');
    }

    public function testSerializeUnserialize(): void
    {
        $unserialized = unserialize(serialize($this->resource));

        self::assertSame(realpath($this->file), $this->resource->getResource());
    }
}
