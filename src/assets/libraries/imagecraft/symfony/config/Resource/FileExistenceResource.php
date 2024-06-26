<?php



/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Config\Resource;

/**
 * FileExistenceResource represents a resource stored on the filesystem.
 * Freshness is only evaluated against resource creation or deletion.
 *
 * The resource can be a file or a directory.
 *
 * @author Charles-Henri Bruyand <charleshenri.bruyand@gmail.com>
 */
class FileExistenceResource implements SelfCheckingResourceInterface, \Serializable
{
    private $resource;

    private $exists;

    /**
     * @param string $resource The file path to the resource
     */
    public function __construct($resource)
    {
        $this->resource = (string) $resource;
        $this->exists = file_exists($resource);
    }

    public function __toString()
    {
        return $this->resource;
    }

    public function getResource()
    {
        return $this->resource;
    }

    public function isFresh($timestamp)
    {
        return file_exists($this->resource) === $this->exists;
    }

    public function serialize()
    {
        return serialize([$this->resource, $this->exists]);
    }

    public function unserialize($serialized): void
    {
        [$this->resource, $this->exists] = unserialize($serialized);
    }
}
