<?php



/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Translation\Tests\Loader;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\Resource\FileResource;
use Symfony\Component\Translation\Exception\NotFoundResourceException;
use Symfony\Component\Translation\Loader\IniFileLoader;

/**
 * @internal
 *
 * @coversNothing
 */
final class IniFileLoaderTest extends TestCase
{
    public function testLoad(): void
    {
        $loader = new IniFileLoader();
        $resource = __DIR__.'/../fixtures/resources.ini';
        $catalogue = $loader->load($resource, 'en', 'domain1');

        self::assertSame(['foo' => 'bar'], $catalogue->all('domain1'));
        self::assertSame('en', $catalogue->getLocale());
        self::assertSame([new FileResource($resource)], $catalogue->getResources());
    }

    public function testLoadDoesNothingIfEmpty(): void
    {
        $loader = new IniFileLoader();
        $resource = __DIR__.'/../fixtures/empty.ini';
        $catalogue = $loader->load($resource, 'en', 'domain1');

        self::assertSame([], $catalogue->all('domain1'));
        self::assertSame('en', $catalogue->getLocale());
        self::assertSame([new FileResource($resource)], $catalogue->getResources());
    }

    public function testLoadNonExistingResource(): void
    {
        $this->expectException(NotFoundResourceException::class);

        $loader = new IniFileLoader();
        $resource = __DIR__.'/../fixtures/non-existing.ini';
        $loader->load($resource, 'en', 'domain1');
    }
}
