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

use Symfony\Component\Config\Resource\DirectoryResource;
use Symfony\Component\Translation\Exception\InvalidResourceException;
use Symfony\Component\Translation\Exception\NotFoundResourceException;
use Symfony\Component\Translation\Loader\IcuResFileLoader;

/**
 * @requires extension intl
 *
 * @internal
 *
 * @coversNothing
 */
final class IcuResFileLoaderTest extends LocalizedTestCase
{
    public function testLoad(): void
    {
        // resource is build using genrb command
        $loader = new IcuResFileLoader();
        $resource = __DIR__.'/../fixtures/resourcebundle/res';
        $catalogue = $loader->load($resource, 'en', 'domain1');

        self::assertSame(['foo' => 'bar'], $catalogue->all('domain1'));
        self::assertSame('en', $catalogue->getLocale());
        self::assertSame([new DirectoryResource($resource)], $catalogue->getResources());
    }

    public function testLoadNonExistingResource(): void
    {
        $this->expectException(NotFoundResourceException::class);

        $loader = new IcuResFileLoader();
        $loader->load(__DIR__.'/../fixtures/non-existing.txt', 'en', 'domain1');
    }

    public function testLoadInvalidResource(): void
    {
        $this->expectException(InvalidResourceException::class);

        $loader = new IcuResFileLoader();
        $loader->load(__DIR__.'/../fixtures/resourcebundle/corrupted', 'en', 'domain1');
    }
}
