<?php



/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Translation\Tests;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Translation\MessageCatalogue;

/**
 * @internal
 *
 * @coversNothing
 */
final class MessageCatalogueTest extends TestCase
{
    public function testGetLocale(): void
    {
        $catalogue = new MessageCatalogue('en');

        self::assertSame('en', $catalogue->getLocale());
    }

    public function testGetDomains(): void
    {
        $catalogue = new MessageCatalogue('en', ['domain1' => [], 'domain2' => []]);

        self::assertSame(['domain1', 'domain2'], $catalogue->getDomains());
    }

    public function testAll(): void
    {
        $catalogue = new MessageCatalogue('en', $messages = ['domain1' => ['foo' => 'foo'], 'domain2' => ['bar' => 'bar']]);

        self::assertSame(['foo' => 'foo'], $catalogue->all('domain1'));
        self::assertSame([], $catalogue->all('domain88'));
        self::assertSame($messages, $catalogue->all());
    }

    public function testHas(): void
    {
        $catalogue = new MessageCatalogue('en', ['domain1' => ['foo' => 'foo'], 'domain2' => ['bar' => 'bar']]);

        self::assertTrue($catalogue->has('foo', 'domain1'));
        self::assertFalse($catalogue->has('bar', 'domain1'));
        self::assertFalse($catalogue->has('foo', 'domain88'));
    }

    public function testGetSet(): void
    {
        $catalogue = new MessageCatalogue('en', ['domain1' => ['foo' => 'foo'], 'domain2' => ['bar' => 'bar']]);
        $catalogue->set('foo1', 'foo1', 'domain1');

        self::assertSame('foo', $catalogue->get('foo', 'domain1'));
        self::assertSame('foo1', $catalogue->get('foo1', 'domain1'));
    }

    public function testAdd(): void
    {
        $catalogue = new MessageCatalogue('en', ['domain1' => ['foo' => 'foo'], 'domain2' => ['bar' => 'bar']]);
        $catalogue->add(['foo1' => 'foo1'], 'domain1');

        self::assertSame('foo', $catalogue->get('foo', 'domain1'));
        self::assertSame('foo1', $catalogue->get('foo1', 'domain1'));

        $catalogue->add(['foo' => 'bar'], 'domain1');
        self::assertSame('bar', $catalogue->get('foo', 'domain1'));
        self::assertSame('foo1', $catalogue->get('foo1', 'domain1'));

        $catalogue->add(['foo' => 'bar'], 'domain88');
        self::assertSame('bar', $catalogue->get('foo', 'domain88'));
    }

    public function testReplace(): void
    {
        $catalogue = new MessageCatalogue('en', ['domain1' => ['foo' => 'foo'], 'domain2' => ['bar' => 'bar']]);
        $catalogue->replace($messages = ['foo1' => 'foo1'], 'domain1');

        self::assertSame($messages, $catalogue->all('domain1'));
    }

    public function testAddCatalogue(): void
    {
        $r = $this->getMockBuilder('Symfony\Component\Config\Resource\ResourceInterface')->getMock();
        $r->expects(self::any())->method('__toString')->willReturn('r');

        $r1 = $this->getMockBuilder('Symfony\Component\Config\Resource\ResourceInterface')->getMock();
        $r1->expects(self::any())->method('__toString')->willReturn('r1');

        $catalogue = new MessageCatalogue('en', ['domain1' => ['foo' => 'foo'], 'domain2' => ['bar' => 'bar']]);
        $catalogue->addResource($r);

        $catalogue1 = new MessageCatalogue('en', ['domain1' => ['foo1' => 'foo1']]);
        $catalogue1->addResource($r1);

        $catalogue->addCatalogue($catalogue1);

        self::assertSame('foo', $catalogue->get('foo', 'domain1'));
        self::assertSame('foo1', $catalogue->get('foo1', 'domain1'));

        self::assertSame([$r, $r1], $catalogue->getResources());
    }

    public function testAddFallbackCatalogue(): void
    {
        $r = $this->getMockBuilder('Symfony\Component\Config\Resource\ResourceInterface')->getMock();
        $r->expects(self::any())->method('__toString')->willReturn('r');

        $r1 = $this->getMockBuilder('Symfony\Component\Config\Resource\ResourceInterface')->getMock();
        $r1->expects(self::any())->method('__toString')->willReturn('r1');

        $r2 = $this->getMockBuilder('Symfony\Component\Config\Resource\ResourceInterface')->getMock();
        $r2->expects(self::any())->method('__toString')->willReturn('r2');

        $catalogue = new MessageCatalogue('fr_FR', ['domain1' => ['foo' => 'foo'], 'domain2' => ['bar' => 'bar']]);
        $catalogue->addResource($r);

        $catalogue1 = new MessageCatalogue('fr', ['domain1' => ['foo' => 'bar', 'foo1' => 'foo1']]);
        $catalogue1->addResource($r1);

        $catalogue2 = new MessageCatalogue('en');
        $catalogue2->addResource($r2);

        $catalogue->addFallbackCatalogue($catalogue1);
        $catalogue1->addFallbackCatalogue($catalogue2);

        self::assertSame('foo', $catalogue->get('foo', 'domain1'));
        self::assertSame('foo1', $catalogue->get('foo1', 'domain1'));

        self::assertSame([$r, $r1, $r2], $catalogue->getResources());
    }

    public function testAddFallbackCatalogueWithParentCircularReference(): void
    {
        $this->expectException(\LogicException::class);

        $main = new MessageCatalogue('en_US');
        $fallback = new MessageCatalogue('fr_FR');

        $fallback->addFallbackCatalogue($main);
        $main->addFallbackCatalogue($fallback);
    }

    public function testAddFallbackCatalogueWithFallbackCircularReference(): void
    {
        $this->expectException(\LogicException::class);

        $fr = new MessageCatalogue('fr');
        $en = new MessageCatalogue('en');
        $es = new MessageCatalogue('es');

        $fr->addFallbackCatalogue($en);
        $es->addFallbackCatalogue($en);
        $en->addFallbackCatalogue($fr);
    }

    public function testAddCatalogueWhenLocaleIsNotTheSameAsTheCurrentOne(): void
    {
        $this->expectException(\LogicException::class);

        $catalogue = new MessageCatalogue('en');
        $catalogue->addCatalogue(new MessageCatalogue('fr', []));
    }

    public function testGetAddResource(): void
    {
        $catalogue = new MessageCatalogue('en');
        $r = $this->getMockBuilder('Symfony\Component\Config\Resource\ResourceInterface')->getMock();
        $r->expects(self::any())->method('__toString')->willReturn('r');
        $catalogue->addResource($r);
        $catalogue->addResource($r);
        $r1 = $this->getMockBuilder('Symfony\Component\Config\Resource\ResourceInterface')->getMock();
        $r1->expects(self::any())->method('__toString')->willReturn('r1');
        $catalogue->addResource($r1);

        self::assertSame([$r, $r1], $catalogue->getResources());
    }

    public function testMetadataDelete(): void
    {
        $catalogue = new MessageCatalogue('en');
        self::assertSame([], $catalogue->getMetadata('', ''), 'Metadata is empty');
        $catalogue->deleteMetadata('key', 'messages');
        $catalogue->deleteMetadata('', 'messages');
        $catalogue->deleteMetadata();
    }

    public function testMetadataSetGetDelete(): void
    {
        $catalogue = new MessageCatalogue('en');
        $catalogue->setMetadata('key', 'value');
        self::assertSame('value', $catalogue->getMetadata('key', 'messages'), "Metadata 'key' = 'value'");

        $catalogue->setMetadata('key2', []);
        self::assertSame([], $catalogue->getMetadata('key2', 'messages'), 'Metadata key2 is array');

        $catalogue->deleteMetadata('key2', 'messages');
        self::assertNull($catalogue->getMetadata('key2', 'messages'), 'Metadata key2 should is deleted.');

        $catalogue->deleteMetadata('key2', 'domain');
        self::assertNull($catalogue->getMetadata('key2', 'domain'), 'Metadata key2 should is deleted.');
    }

    public function testMetadataMerge(): void
    {
        $cat1 = new MessageCatalogue('en');
        $cat1->setMetadata('a', 'b');
        self::assertSame(['messages' => ['a' => 'b']], $cat1->getMetadata('', ''), 'Cat1 contains messages metadata.');

        $cat2 = new MessageCatalogue('en');
        $cat2->setMetadata('b', 'c', 'domain');
        self::assertSame(['domain' => ['b' => 'c']], $cat2->getMetadata('', ''), 'Cat2 contains domain metadata.');

        $cat1->addCatalogue($cat2);
        self::assertSame(['messages' => ['a' => 'b'], 'domain' => ['b' => 'c']], $cat1->getMetadata('', ''), 'Cat1 contains merged metadata.');
    }
}
