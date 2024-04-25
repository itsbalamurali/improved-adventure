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
use Symfony\Component\Translation\Exception\InvalidResourceException;
use Symfony\Component\Translation\Exception\NotFoundResourceException;
use Symfony\Component\Translation\Loader\MoFileLoader;

/**
 * @internal
 *
 * @coversNothing
 */
final class MoFileLoaderTest extends TestCase
{
    public function testLoad(): void
    {
        $loader = new MoFileLoader();
        $resource = __DIR__.'/../fixtures/resources.mo';
        $catalogue = $loader->load($resource, 'en', 'domain1');

        self::assertSame(['foo' => 'bar'], $catalogue->all('domain1'));
        self::assertSame('en', $catalogue->getLocale());
        self::assertSame([new FileResource($resource)], $catalogue->getResources());
    }

    public function testLoadPlurals(): void
    {
        $loader = new MoFileLoader();
        $resource = __DIR__.'/../fixtures/plurals.mo';
        $catalogue = $loader->load($resource, 'en', 'domain1');

        self::assertSame(['foo' => 'bar', 'foos' => '{0} bar|{1} bars'], $catalogue->all('domain1'));
        self::assertSame('en', $catalogue->getLocale());
        self::assertSame([new FileResource($resource)], $catalogue->getResources());
    }

    public function testLoadNonExistingResource(): void
    {
        $this->expectException(NotFoundResourceException::class);

        $loader = new MoFileLoader();
        $resource = __DIR__.'/../fixtures/non-existing.mo';
        $loader->load($resource, 'en', 'domain1');
    }

    public function testLoadInvalidResource(): void
    {
        $this->expectException(InvalidResourceException::class);

        $loader = new MoFileLoader();
        $resource = __DIR__.'/../fixtures/empty.mo';
        $loader->load($resource, 'en', 'domain1');
    }

    public function testLoadEmptyTranslation(): void
    {
        $loader = new MoFileLoader();
        $resource = __DIR__.'/../fixtures/empty-translation.mo';
        $catalogue = $loader->load($resource, 'en', 'message');

        self::assertSame([], $catalogue->all('message'));
        self::assertSame('en', $catalogue->getLocale());
        self::assertSame([new FileResource($resource)], $catalogue->getResources());
    }
}
