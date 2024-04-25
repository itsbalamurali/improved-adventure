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
use Symfony\Component\Config\ConfigCache;
use Symfony\Component\Config\Tests\Resource\ResourceStub;

/**
 * @internal
 *
 * @coversNothing
 */
final class ConfigCacheTest extends TestCase
{
    private $cacheFile;

    protected function setUp(): void
    {
        $this->cacheFile = tempnam(sys_get_temp_dir(), 'config_');
    }

    protected function tearDown(): void
    {
        $files = [$this->cacheFile, $this->cacheFile.'.meta'];

        foreach ($files as $file) {
            if (file_exists($file)) {
                unlink($file);
            }
        }
    }

    /**
     * @dataProvider debugModes
     *
     * @param mixed $debug
     */
    public function testCacheIsNotValidIfNothingHasBeenCached($debug): void
    {
        unlink($this->cacheFile); // remove tempnam() side effect
        $cache = new ConfigCache($this->cacheFile, $debug);

        self::assertFalse($cache->isFresh());
    }

    public function testIsAlwaysFreshInProduction(): void
    {
        $staleResource = new ResourceStub();
        $staleResource->setFresh(false);

        $cache = new ConfigCache($this->cacheFile, false);
        $cache->write('', [$staleResource]);

        self::assertTrue($cache->isFresh());
    }

    /**
     * @dataProvider debugModes
     *
     * @param mixed $debug
     */
    public function testIsFreshWhenNoResourceProvided($debug): void
    {
        $cache = new ConfigCache($this->cacheFile, $debug);
        $cache->write('', []);
        self::assertTrue($cache->isFresh());
    }

    public function testFreshResourceInDebug(): void
    {
        $freshResource = new ResourceStub();
        $freshResource->setFresh(true);

        $cache = new ConfigCache($this->cacheFile, true);
        $cache->write('', [$freshResource]);

        self::assertTrue($cache->isFresh());
    }

    public function testStaleResourceInDebug(): void
    {
        $staleResource = new ResourceStub();
        $staleResource->setFresh(false);

        $cache = new ConfigCache($this->cacheFile, true);
        $cache->write('', [$staleResource]);

        self::assertFalse($cache->isFresh());
    }

    public static function debugModes(): iterable
    {
        return [
            [true],
            [false],
        ];
    }
}
