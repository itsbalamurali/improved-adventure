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
use Symfony\Component\Config\Loader\LoaderResolver;

/**
 * @internal
 *
 * @coversNothing
 */
final class LoaderResolverTest extends TestCase
{
    public function testConstructor(): void
    {
        $resolver = new LoaderResolver([
            $loader = $this->getMockBuilder('Symfony\Component\Config\Loader\LoaderInterface')->getMock(),
        ]);

        self::assertSame([$loader], $resolver->getLoaders(), '__construct() takes an array of loaders as its first argument');
    }

    public function testResolve(): void
    {
        $loader = $this->getMockBuilder('Symfony\Component\Config\Loader\LoaderInterface')->getMock();
        $resolver = new LoaderResolver([$loader]);
        self::assertFalse($resolver->resolve('foo.foo'), '->resolve() returns false if no loader is able to load the resource');

        $loader = $this->getMockBuilder('Symfony\Component\Config\Loader\LoaderInterface')->getMock();
        $loader->expects(self::once())->method('supports')->willReturn(true);
        $resolver = new LoaderResolver([$loader]);
        self::assertSame($loader, $resolver->resolve(static function (): void {}), '->resolve() returns the loader for the given resource');
    }

    public function testLoaders(): void
    {
        $resolver = new LoaderResolver();
        $resolver->addLoader($loader = $this->getMockBuilder('Symfony\Component\Config\Loader\LoaderInterface')->getMock());

        self::assertSame([$loader], $resolver->getLoaders(), 'addLoader() adds a loader');
    }
}
