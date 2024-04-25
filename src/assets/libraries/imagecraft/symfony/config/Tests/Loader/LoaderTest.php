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
use Symfony\Component\Config\Loader\Loader;

/**
 * @internal
 *
 * @coversNothing
 */
final class LoaderTest extends TestCase
{
    public function testGetSetResolver(): void
    {
        $resolver = $this->getMockBuilder('Symfony\Component\Config\Loader\LoaderResolverInterface')->getMock();

        $loader = new ProjectLoader1();
        $loader->setResolver($resolver);

        self::assertSame($resolver, $loader->getResolver(), '->setResolver() sets the resolver loader');
    }

    public function testResolve(): void
    {
        $resolvedLoader = $this->getMockBuilder('Symfony\Component\Config\Loader\LoaderInterface')->getMock();

        $resolver = $this->getMockBuilder('Symfony\Component\Config\Loader\LoaderResolverInterface')->getMock();
        $resolver->expects(self::once())
            ->method('resolve')
            ->with('foo.xml')
            ->willReturn($resolvedLoader)
        ;

        $loader = new ProjectLoader1();
        $loader->setResolver($resolver);

        self::assertSame($loader, $loader->resolve('foo.foo'), '->resolve() finds a loader');
        self::assertSame($resolvedLoader, $loader->resolve('foo.xml'), '->resolve() finds a loader');
    }

    public function testResolveWhenResolverCannotFindLoader(): void
    {
        $this->expectException(FileLoaderLoadException::class);

        $resolver = $this->getMockBuilder('Symfony\Component\Config\Loader\LoaderResolverInterface')->getMock();
        $resolver->expects(self::once())
            ->method('resolve')
            ->with('FOOBAR')
            ->willReturn(false)
        ;

        $loader = new ProjectLoader1();
        $loader->setResolver($resolver);

        $loader->resolve('FOOBAR');
    }

    public function testImport(): void
    {
        $resolvedLoader = $this->getMockBuilder('Symfony\Component\Config\Loader\LoaderInterface')->getMock();
        $resolvedLoader->expects(self::once())
            ->method('load')
            ->with('foo')
            ->willReturn('yes')
        ;

        $resolver = $this->getMockBuilder('Symfony\Component\Config\Loader\LoaderResolverInterface')->getMock();
        $resolver->expects(self::once())
            ->method('resolve')
            ->with('foo')
            ->willReturn($resolvedLoader)
        ;

        $loader = new ProjectLoader1();
        $loader->setResolver($resolver);

        self::assertSame('yes', $loader->import('foo'));
    }

    public function testImportWithType(): void
    {
        $resolvedLoader = $this->getMockBuilder('Symfony\Component\Config\Loader\LoaderInterface')->getMock();
        $resolvedLoader->expects(self::once())
            ->method('load')
            ->with('foo', 'bar')
            ->willReturn('yes')
        ;

        $resolver = $this->getMockBuilder('Symfony\Component\Config\Loader\LoaderResolverInterface')->getMock();
        $resolver->expects(self::once())
            ->method('resolve')
            ->with('foo', 'bar')
            ->willReturn($resolvedLoader)
        ;

        $loader = new ProjectLoader1();
        $loader->setResolver($resolver);

        self::assertSame('yes', $loader->import('foo', 'bar'));
    }
}

class ProjectLoader1 extends Loader
{
    public function load($resource, $type = null): void {}

    public function supports($resource, $type = null)
    {
        return \is_string($resource) && 'foo' === pathinfo($resource, PATHINFO_EXTENSION);
    }

    public function getType(): void {}
}
