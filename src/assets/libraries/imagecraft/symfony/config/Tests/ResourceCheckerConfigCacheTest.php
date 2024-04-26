<?php



/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Config\Tests;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\Resource\FileResource;
use Symfony\Component\Config\ResourceCheckerConfigCache;
use Symfony\Component\Config\Tests\Resource\ResourceStub;

/**
 * @internal
 *
 * @coversNothing
 */
final class ResourceCheckerConfigCacheTest extends TestCase
{
    private $cacheFile;

    protected function setUp(): void
    {
        $this->cacheFile = tempnam(sys_get_temp_dir(), 'config_');
    }

    protected function tearDown(): void
    {
        $files = [$this->cacheFile, "{$this->cacheFile}.meta"];

        foreach ($files as $file) {
            if (file_exists($file)) {
                unlink($file);
            }
        }
    }

    public function testGetPath(): void
    {
        $cache = new ResourceCheckerConfigCache($this->cacheFile);

        self::assertSame($this->cacheFile, $cache->getPath());
    }

    public function testCacheIsNotFreshIfEmpty(): void
    {
        $checker = $this->getMockBuilder('\Symfony\Component\Config\ResourceCheckerInterface')->getMock()
            ->expects(self::never())->method('supports')
        ;

        /* If there is nothing in the cache, it needs to be filled (and thus it's not fresh).
            It does not matter if you provide checkers or not. */

        unlink($this->cacheFile); // remove tempnam() side effect
        $cache = new ResourceCheckerConfigCache($this->cacheFile, [$checker]);

        self::assertFalse($cache->isFresh());
    }

    public function testCacheIsFreshIfNocheckerProvided(): void
    {
        /* For example in prod mode, you may choose not to run any checkers
           at all. In that case, the cache should always be considered fresh. */
        $cache = new ResourceCheckerConfigCache($this->cacheFile);
        self::assertTrue($cache->isFresh());
    }

    public function testResourcesWithoutcheckersAreIgnoredAndConsideredFresh(): void
    {
        // As in the previous test, but this time we have a resource.
        $cache = new ResourceCheckerConfigCache($this->cacheFile);
        $cache->write('', [new ResourceStub()]);

        self::assertTrue($cache->isFresh()); // no (matching) ResourceChecker passed
    }

    public function testIsFreshWithchecker(): void
    {
        $checker = $this->getMockBuilder('\Symfony\Component\Config\ResourceCheckerInterface')->getMock();

        $checker->expects(self::once())
            ->method('supports')
            ->willReturn(true)
        ;

        $checker->expects(self::once())
            ->method('isFresh')
            ->willReturn(true)
        ;

        $cache = new ResourceCheckerConfigCache($this->cacheFile, [$checker]);
        $cache->write('', [new ResourceStub()]);

        self::assertTrue($cache->isFresh());
    }

    public function testIsNotFreshWithchecker(): void
    {
        $checker = $this->getMockBuilder('\Symfony\Component\Config\ResourceCheckerInterface')->getMock();

        $checker->expects(self::once())
            ->method('supports')
            ->willReturn(true)
        ;

        $checker->expects(self::once())
            ->method('isFresh')
            ->willReturn(false)
        ;

        $cache = new ResourceCheckerConfigCache($this->cacheFile, [$checker]);
        $cache->write('', [new ResourceStub()]);

        self::assertFalse($cache->isFresh());
    }

    public function testCacheIsNotFreshWhenUnserializeFails(): void
    {
        $checker = $this->getMockBuilder('\Symfony\Component\Config\ResourceCheckerInterface')->getMock();
        $cache = new ResourceCheckerConfigCache($this->cacheFile, [$checker]);
        $cache->write('foo', [new FileResource(__FILE__)]);

        $metaFile = "{$this->cacheFile}.meta";
        file_put_contents($metaFile, str_replace('FileResource', 'ClassNotHere', file_get_contents($metaFile)));

        self::assertFalse($cache->isFresh());
    }

    public function testCacheKeepsContent(): void
    {
        $cache = new ResourceCheckerConfigCache($this->cacheFile);
        $cache->write('FOOBAR');

        self::assertSame('FOOBAR', file_get_contents($cache->getPath()));
    }

    public function testCacheIsNotFreshIfNotExistsMetaFile(): void
    {
        $checker = $this->getMockBuilder('\Symfony\Component\Config\ResourceCheckerInterface')->getMock();
        $cache = new ResourceCheckerConfigCache($this->cacheFile, [$checker]);
        $cache->write('foo', [new FileResource(__FILE__)]);

        $metaFile = "{$this->cacheFile}.meta";
        unlink($metaFile);

        self::assertFalse($cache->isFresh());
    }
}
