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

use Symfony\Component\Config\Resource\FileResource;
use Symfony\Component\Translation\Exception\InvalidResourceException;
use Symfony\Component\Translation\Exception\NotFoundResourceException;
use Symfony\Component\Translation\Loader\IcuDatFileLoader;

/**
 * @requires extension intl
 *
 * @internal
 *
 * @coversNothing
 */
final class IcuDatFileLoaderTest extends LocalizedTestCase
{
    public function testLoadInvalidResource(): void
    {
        $this->expectException(InvalidResourceException::class);

        $loader = new IcuDatFileLoader();
        $loader->load(__DIR__.'/../fixtures/resourcebundle/corrupted/resources', 'es', 'domain2');
    }

    public function testDatEnglishLoad(): void
    {
        // bundled resource is build using pkgdata command which at least in ICU 4.2 comes in extremely! buggy form
        // you must specify an temporary build directory which is not the same as current directory and
        // MUST reside on the same partition. pkgdata -p resources -T /srv -d.packagelist.txt
        $loader = new IcuDatFileLoader();
        $resource = __DIR__.'/../fixtures/resourcebundle/dat/resources';
        $catalogue = $loader->load($resource, 'en', 'domain1');

        self::assertSame(['symfony' => 'Symfony 2 is great'], $catalogue->all('domain1'));
        self::assertSame('en', $catalogue->getLocale());
        self::assertSame([new FileResource($resource.'.dat')], $catalogue->getResources());
    }

    public function testDatFrenchLoad(): void
    {
        $loader = new IcuDatFileLoader();
        $resource = __DIR__.'/../fixtures/resourcebundle/dat/resources';
        $catalogue = $loader->load($resource, 'fr', 'domain1');

        self::assertSame(['symfony' => 'Symfony 2 est génial'], $catalogue->all('domain1'));
        self::assertSame('fr', $catalogue->getLocale());
        self::assertSame([new FileResource($resource.'.dat')], $catalogue->getResources());
    }

    public function testLoadNonExistingResource(): void
    {
        $this->expectException(NotFoundResourceException::class);

        $loader = new IcuDatFileLoader();
        $loader->load(__DIR__.'/../fixtures/non-existing.txt', 'en', 'domain1');
    }
}
