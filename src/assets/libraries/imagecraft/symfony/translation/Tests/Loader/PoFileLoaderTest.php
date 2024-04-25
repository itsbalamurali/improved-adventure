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
use Symfony\Component\Translation\Loader\PoFileLoader;

/**
 * @internal
 *
 * @coversNothing
 */
final class PoFileLoaderTest extends TestCase
{
    public function testLoad(): void
    {
        $loader = new PoFileLoader();
        $resource = __DIR__.'/../fixtures/resources.po';
        $catalogue = $loader->load($resource, 'en', 'domain1');

        self::assertSame(['foo' => 'bar'], $catalogue->all('domain1'));
        self::assertSame('en', $catalogue->getLocale());
        self::assertSame([new FileResource($resource)], $catalogue->getResources());
    }

    public function testLoadPlurals(): void
    {
        $loader = new PoFileLoader();
        $resource = __DIR__.'/../fixtures/plurals.po';
        $catalogue = $loader->load($resource, 'en', 'domain1');

        self::assertSame(['foo' => 'bar', 'foos' => 'bar|bars'], $catalogue->all('domain1'));
        self::assertSame('en', $catalogue->getLocale());
        self::assertSame([new FileResource($resource)], $catalogue->getResources());
    }

    public function testLoadDoesNothingIfEmpty(): void
    {
        $loader = new PoFileLoader();
        $resource = __DIR__.'/../fixtures/empty.po';
        $catalogue = $loader->load($resource, 'en', 'domain1');

        self::assertSame([], $catalogue->all('domain1'));
        self::assertSame('en', $catalogue->getLocale());
        self::assertSame([new FileResource($resource)], $catalogue->getResources());
    }

    public function testLoadNonExistingResource(): void
    {
        $this->expectException(NotFoundResourceException::class);

        $loader = new PoFileLoader();
        $resource = __DIR__.'/../fixtures/non-existing.po';
        $loader->load($resource, 'en', 'domain1');
    }

    public function testLoadEmptyTranslation(): void
    {
        $loader = new PoFileLoader();
        $resource = __DIR__.'/../fixtures/empty-translation.po';
        $catalogue = $loader->load($resource, 'en', 'domain1');

        self::assertSame(['foo' => ''], $catalogue->all('domain1'));
        self::assertSame('en', $catalogue->getLocale());
        self::assertSame([new FileResource($resource)], $catalogue->getResources());
    }

    public function testEscapedId(): void
    {
        $loader = new PoFileLoader();
        $resource = __DIR__.'/../fixtures/escaped-id.po';
        $catalogue = $loader->load($resource, 'en', 'domain1');

        $messages = $catalogue->all('domain1');
        self::assertArrayHasKey('escaped "foo"', $messages);
        self::assertSame('escaped "bar"', $messages['escaped "foo"']);
    }

    public function testEscapedIdPlurals(): void
    {
        $loader = new PoFileLoader();
        $resource = __DIR__.'/../fixtures/escaped-id-plurals.po';
        $catalogue = $loader->load($resource, 'en', 'domain1');

        $messages = $catalogue->all('domain1');
        self::assertArrayHasKey('escaped "foo"', $messages);
        self::assertArrayHasKey('escaped "foos"', $messages);
        self::assertSame('escaped "bar"', $messages['escaped "foo"']);
        self::assertSame('escaped "bar"|escaped "bars"', $messages['escaped "foos"']);
    }

    public function testSkipFuzzyTranslations(): void
    {
        $loader = new PoFileLoader();
        $resource = __DIR__.'/../fixtures/fuzzy-translations.po';
        $catalogue = $loader->load($resource, 'en', 'domain1');

        $messages = $catalogue->all('domain1');
        self::assertArrayHasKey('foo1', $messages);
        self::assertArrayNotHasKey('foo2', $messages);
        self::assertArrayHasKey('foo3', $messages);
    }
}
