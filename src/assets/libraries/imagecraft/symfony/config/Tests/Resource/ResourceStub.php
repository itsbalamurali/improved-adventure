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

use Symfony\Component\Config\Resource\SelfCheckingResourceInterface;

class ResourceStub implements SelfCheckingResourceInterface
{
    private $fresh = true;

    public function __toString()
    {
        return 'stub';
    }

    public function setFresh($isFresh): void
    {
        $this->fresh = $isFresh;
    }

    public function isFresh($timestamp)
    {
        return $this->fresh;
    }

    public function getResource()
    {
        return 'stub';
    }
}
