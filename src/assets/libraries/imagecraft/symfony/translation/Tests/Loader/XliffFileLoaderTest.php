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
use Symfony\Component\Translation\Loader\XliffFileLoader;

/**
 * @internal
 *
 * @coversNothing
 */
final class XliffFileLoaderTest extends TestCase
{
    public function testLoad(): void
    {
        $loader = new XliffFileLoader();
        $resource = __DIR__.'/../fixtures/resources.xlf';
        $catalogue = $loader->load($resource, 'en', 'domain1');

        self::assertSame('en', $catalogue->getLocale());
        self::assertSame([new FileResource($resource)], $catalogue->getResources());
        self::assertSame([], libxml_get_errors());
        self::assertContainsOnly('string', $catalogue->all('domain1'));
    }

    public function testLoadWithInternalErrorsEnabled(): void
    {
        $internalErrors = libxml_use_internal_errors(true);

        self::assertSame([], libxml_get_errors());

        $loader = new XliffFileLoader();
        $resource = __DIR__.'/../fixtures/resources.xlf';
        $catalogue = $loader->load($resource, 'en', 'domain1');

        self::assertSame('en', $catalogue->getLocale());
        self::assertSame([new FileResource($resource)], $catalogue->getResources());
        self::assertSame([], libxml_get_errors());

        libxml_clear_errors();
        libxml_use_internal_errors($internalErrors);
    }

    public function testLoadWithExternalEntitiesDisabled(): void
    {
        $disableEntities = libxml_disable_entity_loader(true);

        $loader = new XliffFileLoader();
        $resource = __DIR__.'/../fixtures/resources.xlf';
        $catalogue = $loader->load($resource, 'en', 'domain1');

        libxml_disable_entity_loader($disableEntities);

        self::assertSame('en', $catalogue->getLocale());
        self::assertSame([new FileResource($resource)], $catalogue->getResources());
    }

    public function testLoadWithResname(): void
    {
        $loader = new XliffFileLoader();
        $catalogue = $loader->load(__DIR__.'/../fixtures/resname.xlf', 'en', 'domain1');

        self::assertSame(['foo' => 'bar', 'bar' => 'baz', 'baz' => 'foo', 'qux' => 'qux source'], $catalogue->all('domain1'));
    }

    public function testIncompleteResource(): void
    {
        $loader = new XliffFileLoader();
        $catalogue = $loader->load(__DIR__.'/../fixtures/resources.xlf', 'en', 'domain1');

        self::assertSame(['foo' => 'bar', 'extra' => 'extra', 'key' => '', 'test' => 'with'], $catalogue->all('domain1'));
    }

    public function testEncoding(): void
    {
        $loader = new XliffFileLoader();
        $catalogue = $loader->load(__DIR__.'/../fixtures/encoding.xlf', 'en', 'domain1');

        self::assertSame(utf8_decode('föö'), $catalogue->get('bar', 'domain1'));
        self::assertSame(utf8_decode('bär'), $catalogue->get('foo', 'domain1'));
        self::assertSame(['notes' => [['content' => utf8_decode('bäz')]]], $catalogue->getMetadata('foo', 'domain1'));
    }

    public function testTargetAttributesAreStoredCorrectly(): void
    {
        $loader = new XliffFileLoader();
        $catalogue = $loader->load(__DIR__.'/../fixtures/with-attributes.xlf', 'en', 'domain1');

        $metadata = $catalogue->getMetadata('foo', 'domain1');
        self::assertSame('translated', $metadata['target-attributes']['state']);
    }

    public function testLoadInvalidResource(): void
    {
        $this->expectException(InvalidResourceException::class);

        $loader = new XliffFileLoader();
        $loader->load(__DIR__.'/../fixtures/resources.php', 'en', 'domain1');
    }

    public function testLoadResourceDoesNotValidate(): void
    {
        $this->expectException(InvalidResourceException::class);

        $loader = new XliffFileLoader();
        $loader->load(__DIR__.'/../fixtures/non-valid.xlf', 'en', 'domain1');
    }

    public function testLoadNonExistingResource(): void
    {
        $this->expectException(NotFoundResourceException::class);

        $loader = new XliffFileLoader();
        $resource = __DIR__.'/../fixtures/non-existing.xlf';
        $loader->load($resource, 'en', 'domain1');
    }

    public function testLoadThrowsAnExceptionIfFileNotLocal(): void
    {
        $this->expectException(InvalidResourceException::class);

        $loader = new XliffFileLoader();
        $resource = 'http://example.com/resources.xlf';
        $loader->load($resource, 'en', 'domain1');
    }

    public function testDocTypeIsNotAllowed(): void
    {
        $this->expectException(InvalidResourceException::class);
        $this->expectExceptionMessage('Document types are not allowed.');

        $loader = new XliffFileLoader();
        $loader->load(__DIR__.'/../fixtures/withdoctype.xlf', 'en', 'domain1');
    }

    public function testParseEmptyFile(): void
    {
        $loader = new XliffFileLoader();
        $resource = __DIR__.'/../fixtures/empty.xlf';

        if (method_exists($this, 'expectException')) {
            $this->expectException('Symfony\Component\Translation\Exception\InvalidResourceException');
            $this->expectExceptionMessage(sprintf('Unable to load "%s":', $resource));
        } else {
            $this->expectException('Symfony\Component\Translation\Exception\InvalidResourceException');
            $this->expectExceptionMessage(sprintf('Unable to load "%s":', $resource));
        }

        $loader->load($resource, 'en', 'domain1');
    }

    public function testLoadNotes(): void
    {
        $loader = new XliffFileLoader();
        $catalogue = $loader->load(__DIR__.'/../fixtures/withnote.xlf', 'en', 'domain1');

        self::assertSame(['notes' => [['priority' => 1, 'content' => 'foo']]], $catalogue->getMetadata('foo', 'domain1'));
        // message without target
        self::assertSame(['notes' => [['content' => 'bar', 'from' => 'foo']]], $catalogue->getMetadata('extra', 'domain1'));
        // message with empty target
        self::assertSame(['notes' => [['content' => 'baz'], ['priority' => 2, 'from' => 'bar', 'content' => 'qux']]], $catalogue->getMetadata('key', 'domain1'));
    }

    public function testLoadVersion2(): void
    {
        $loader = new XliffFileLoader();
        $resource = __DIR__.'/../fixtures/resources-2.0.xlf';
        $catalogue = $loader->load($resource, 'en', 'domain1');

        self::assertSame('en', $catalogue->getLocale());
        self::assertSame([new FileResource($resource)], $catalogue->getResources());
        self::assertSame([], libxml_get_errors());

        $domains = $catalogue->all();
        self::assertCount(3, $domains['domain1']);
        self::assertContainsOnly('string', $catalogue->all('domain1'));

        // target attributes
        self::assertSame(['target-attributes' => ['order' => 1]], $catalogue->getMetadata('bar', 'domain1'));
    }
}
