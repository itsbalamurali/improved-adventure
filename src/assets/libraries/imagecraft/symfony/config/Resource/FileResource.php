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
 * FileResource represents a resource stored on the filesystem.
 *
 * The resource can be a file or a directory.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class FileResource implements SelfCheckingResourceInterface, \Serializable
{
    /**
     * @var false|string
     */
    private $resource;

    /**
     * @param string $resource The file path to the resource
     */
    public function __construct($resource)
    {
        $this->resource = realpath($resource) ?: (file_exists($resource) ? $resource : false);
    }

    public function __toString()
    {
        return (string) $this->resource;
    }

    public function getResource()
    {
        return $this->resource;
    }

    public function isFresh($timestamp)
    {
        if (false === $this->resource || !file_exists($this->resource)) {
            return false;
        }

        return filemtime($this->resource) <= $timestamp;
    }

    public function serialize()
    {
        return serialize($this->resource);
    }

    public function unserialize($serialized): void
    {
        $this->resource = unserialize($serialized);
    }
}
