<?php



/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Config\Tests;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\FileLocator;

/**
 * @internal
 *
 * @coversNothing
 */
final class FileLocatorTest extends TestCase
{
    /**
     * @dataProvider provideIsAbsolutePathCases
     *
     * @param mixed $path
     */
    public function testIsAbsolutePath($path): void
    {
        $loader = new FileLocator([]);
        $r = new \ReflectionObject($loader);
        $m = $r->getMethod('isAbsolutePath');
        $m->setAccessible(true);

        self::assertTrue($m->invoke($loader, $path), '->isAbsolutePath() returns true for an absolute path');
    }

    public static function provideIsAbsolutePathCases(): iterable
    {
        return [
            ['/foo.xml'],
            ['c:\\\\foo.xml'],
            ['c:/foo.xml'],
            ['\\server\\foo.xml'],
            ['https://server/foo.xml'],
            ['phar://server/foo.xml'],
        ];
    }

    public function testLocate(): void
    {
        $loader = new FileLocator(__DIR__.'/Fixtures');

        self::assertSame(
            __DIR__.\DIRECTORY_SEPARATOR.'FileLocatorTest.php',
            $loader->locate('FileLocatorTest.php', __DIR__),
            '->locate() returns the absolute filename if the file exists in the given path'
        );

        self::assertSame(
            __DIR__.'/Fixtures'.\DIRECTORY_SEPARATOR.'foo.xml',
            $loader->locate('foo.xml', __DIR__),
            '->locate() returns the absolute filename if the file exists in one of the paths given in the constructor'
        );

        self::assertSame(
            __DIR__.'/Fixtures'.\DIRECTORY_SEPARATOR.'foo.xml',
            $loader->locate(__DIR__.'/Fixtures'.\DIRECTORY_SEPARATOR.'foo.xml', __DIR__),
            '->locate() returns the absolute filename if the file exists in one of the paths given in the constructor'
        );

        $loader = new FileLocator([__DIR__.'/Fixtures', __DIR__.'/Fixtures/Again']);

        self::assertSame(
            [__DIR__.'/Fixtures'.\DIRECTORY_SEPARATOR.'foo.xml', __DIR__.'/Fixtures/Again'.\DIRECTORY_SEPARATOR.'foo.xml'],
            $loader->locate('foo.xml', __DIR__, false),
            '->locate() returns an array of absolute filenames'
        );

        self::assertSame(
            [__DIR__.'/Fixtures'.\DIRECTORY_SEPARATOR.'foo.xml', __DIR__.'/Fixtures/Again'.\DIRECTORY_SEPARATOR.'foo.xml'],
            $loader->locate('foo.xml', __DIR__.'/Fixtures', false),
            '->locate() returns an array of absolute filenames'
        );

        $loader = new FileLocator(__DIR__.'/Fixtures/Again');

        self::assertSame(
            [__DIR__.'/Fixtures'.\DIRECTORY_SEPARATOR.'foo.xml', __DIR__.'/Fixtures/Again'.\DIRECTORY_SEPARATOR.'foo.xml'],
            $loader->locate('foo.xml', __DIR__.'/Fixtures', false),
            '->locate() returns an array of absolute filenames'
        );
    }

    public function testLocateThrowsAnExceptionIfTheFileDoesNotExists(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('The file "foobar.xml" does not exist');

        $loader = new FileLocator([__DIR__.'/Fixtures']);

        $loader->locate('foobar.xml', __DIR__);
    }

    public function testLocateThrowsAnExceptionIfTheFileDoesNotExistsInAbsolutePath(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $loader = new FileLocator([__DIR__.'/Fixtures']);

        $loader->locate(__DIR__.'/Fixtures/foobar.xml', __DIR__);
    }

    public function testLocateEmpty(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('An empty file name is not valid to be located.');

        $loader = new FileLocator([__DIR__.'/Fixtures']);

        $loader->locate(null, __DIR__);
    }
}
