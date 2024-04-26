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
use Symfony\Component\Config\Resource\FileExistenceResource;

/**
 * @internal
 *
 * @coversNothing
 */
final class FileExistenceResourceTest extends TestCase
{
    protected $resource;
    protected $file;
    protected $time;

    protected function setUp(): void
    {
        $this->file = realpath(sys_get_temp_dir()).'/tmp.xml';
        $this->time = time();
        $this->resource = new FileExistenceResource($this->file);
    }

    protected function tearDown(): void
    {
        if (file_exists($this->file)) {
            unlink($this->file);
        }
    }

    public function testToString(): void
    {
        self::assertSame($this->file, (string) $this->resource);
    }

    public function testGetResource(): void
    {
        self::assertSame($this->file, $this->resource->getResource(), '->getResource() returns the path to the resource');
    }

    public function testIsFreshWithExistingResource(): void
    {
        touch($this->file, $this->time);
        $serialized = serialize(new FileExistenceResource($this->file));

        $resource = unserialize($serialized);
        self::assertTrue($resource->isFresh($this->time), '->isFresh() returns true if the resource is still present');

        unlink($this->file);
        $resource = unserialize($serialized);
        self::assertFalse($resource->isFresh($this->time), '->isFresh() returns false if the resource has been deleted');
    }

    public function testIsFreshWithAbsentResource(): void
    {
        $serialized = serialize(new FileExistenceResource($this->file));

        $resource = unserialize($serialized);
        self::assertTrue($resource->isFresh($this->time), '->isFresh() returns true if the resource is still absent');

        touch($this->file, $this->time);
        $resource = unserialize($serialized);
        self::assertFalse($resource->isFresh($this->time), '->isFresh() returns false if the resource has been created');
    }
}
