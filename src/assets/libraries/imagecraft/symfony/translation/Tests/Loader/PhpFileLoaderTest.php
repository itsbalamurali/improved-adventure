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
use Symfony\Component\Translation\Loader\PhpFileLoader;

/**
 * @internal
 *
 * @coversNothing
 */
final class PhpFileLoaderTest extends TestCase
{
    public function testLoad(): void
    {
        $loader = new PhpFileLoader();
        $resource = __DIR__.'/../fixtures/resources.php';
        $catalogue = $loader->load($resource, 'en', 'domain1');

        self::assertSame(['foo' => 'bar'], $catalogue->all('domain1'));
        self::assertSame('en', $catalogue->getLocale());
        self::assertSame([new FileResource($resource)], $catalogue->getResources());
    }

    public function testLoadNonExistingResource(): void
    {
        $this->expectException(NotFoundResourceException::class);

        $loader = new PhpFileLoader();
        $resource = __DIR__.'/../fixtures/non-existing.php';
        $loader->load($resource, 'en', 'domain1');
    }

    public function testLoadThrowsAnExceptionIfFileNotLocal(): void
    {
        $this->expectException(InvalidResourceException::class);

        $loader = new PhpFileLoader();
        $resource = 'http://example.com/resources.php';
        $loader->load($resource, 'en', 'domain1');
    }
}
