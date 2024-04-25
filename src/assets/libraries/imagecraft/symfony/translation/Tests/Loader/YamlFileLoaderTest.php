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
use Symfony\Component\Translation\Loader\YamlFileLoader;

/**
 * @internal
 *
 * @coversNothing
 */
final class YamlFileLoaderTest extends TestCase
{
    public function testLoad(): void
    {
        $loader = new YamlFileLoader();
        $resource = __DIR__.'/../fixtures/resources.yml';
        $catalogue = $loader->load($resource, 'en', 'domain1');

        self::assertSame(['foo' => 'bar'], $catalogue->all('domain1'));
        self::assertSame('en', $catalogue->getLocale());
        self::assertSame([new FileResource($resource)], $catalogue->getResources());
    }

    public function testLoadDoesNothingIfEmpty(): void
    {
        $loader = new YamlFileLoader();
        $resource = __DIR__.'/../fixtures/empty.yml';
        $catalogue = $loader->load($resource, 'en', 'domain1');

        self::assertSame([], $catalogue->all('domain1'));
        self::assertSame('en', $catalogue->getLocale());
        self::assertSame([new FileResource($resource)], $catalogue->getResources());
    }

    public function testLoadNonExistingResource(): void
    {
        $this->expectException(NotFoundResourceException::class);

        $loader = new YamlFileLoader();
        $resource = __DIR__.'/../fixtures/non-existing.yml';
        $loader->load($resource, 'en', 'domain1');
    }

    public function testLoadThrowsAnExceptionIfFileNotLocal(): void
    {
        $this->expectException(InvalidResourceException::class);

        $loader = new YamlFileLoader();
        $resource = 'http://example.com/resources.yml';
        $loader->load($resource, 'en', 'domain1');
    }

    public function testLoadThrowsAnExceptionIfNotAnArray(): void
    {
        $this->expectException(InvalidResourceException::class);

        $loader = new YamlFileLoader();
        $resource = __DIR__.'/../fixtures/non-valid.yml';
        $loader->load($resource, 'en', 'domain1');
    }
}
