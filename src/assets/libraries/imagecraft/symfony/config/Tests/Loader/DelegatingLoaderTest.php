<?php



/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Config\Tests\Loader;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\Exception\FileLoaderLoadException;
use Symfony\Component\Config\Loader\DelegatingLoader;
use Symfony\Component\Config\Loader\LoaderResolver;

/**
 * @internal
 *
 * @coversNothing
 */
final class DelegatingLoaderTest extends TestCase
{
    public function testConstructor(): void
    {
        $loader = new DelegatingLoader($resolver = new LoaderResolver());
        self::assertTrue(true, '__construct() takes a loader resolver as its first argument');
    }

    public function testGetSetResolver(): void
    {
        $resolver = new LoaderResolver();
        $loader = new DelegatingLoader($resolver);
        self::assertSame($resolver, $loader->getResolver(), '->getResolver() gets the resolver loader');
        $loader->setResolver($resolver = new LoaderResolver());
        self::assertSame($resolver, $loader->getResolver(), '->setResolver() sets the resolver loader');
    }

    public function testSupports(): void
    {
        $loader1 = $this->getMockBuilder('Symfony\Component\Config\Loader\LoaderInterface')->getMock();
        $loader1->expects(self::once())->method('supports')->willReturn(true);
        $loader = new DelegatingLoader(new LoaderResolver([$loader1]));
        self::assertTrue($loader->supports('foo.xml'), '->supports() returns true if the resource is loadable');

        $loader1 = $this->getMockBuilder('Symfony\Component\Config\Loader\LoaderInterface')->getMock();
        $loader1->expects(self::once())->method('supports')->willReturn(false);
        $loader = new DelegatingLoader(new LoaderResolver([$loader1]));
        self::assertFalse($loader->supports('foo.foo'), '->supports() returns false if the resource is not loadable');
    }

    public function testLoad(): void
    {
        $loader = $this->getMockBuilder('Symfony\Component\Config\Loader\LoaderInterface')->getMock();
        $loader->expects(self::once())->method('supports')->willReturn(true);
        $loader->expects(self::once())->method('load');
        $resolver = new LoaderResolver([$loader]);
        $loader = new DelegatingLoader($resolver);

        $loader->load('foo');
    }

    public function testLoadThrowsAnExceptionIfTheResourceCannotBeLoaded(): void
    {
        $this->expectException(FileLoaderLoadException::class);

        $loader = $this->getMockBuilder('Symfony\Component\Config\Loader\LoaderInterface')->getMock();
        $loader->expects(self::once())->method('supports')->willReturn(false);
        $resolver = new LoaderResolver([$loader]);
        $loader = new DelegatingLoader($resolver);

        $loader->load('foo');
    }
}
