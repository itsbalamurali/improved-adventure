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
use Symfony\Component\Config\Loader\FileLoader;
use Symfony\Component\Config\Loader\LoaderResolver;

/**
 * @internal
 *
 * @coversNothing
 */
final class FileLoaderTest extends TestCase
{
    public function testImportWithFileLocatorDelegation(): void
    {
        $locatorMock = $this->getMockBuilder('Symfony\Component\Config\FileLocatorInterface')->getMock();

        $locatorMockForAdditionalLoader = $this->getMockBuilder('Symfony\Component\Config\FileLocatorInterface')->getMock();
        $locatorMockForAdditionalLoader->expects(self::any())->method('locate')->will(self::onConsecutiveCalls(
            ['path/to/file1'],                    // Default
            ['path/to/file1', 'path/to/file2'],   // First is imported
            ['path/to/file1', 'path/to/file2'],   // Second is imported
            ['path/to/file1'],                    // Exception
            ['path/to/file1', 'path/to/file2']    // Exception
        ));

        $fileLoader = new TestFileLoader($locatorMock);
        $fileLoader->setSupports(false);
        $fileLoader->setCurrentDir('.');

        $additionalLoader = new TestFileLoader($locatorMockForAdditionalLoader);
        $additionalLoader->setCurrentDir('.');

        $fileLoader->setResolver($loaderResolver = new LoaderResolver([$fileLoader, $additionalLoader]));

        // Default case
        self::assertSame('path/to/file1', $fileLoader->import('my_resource'));

        // Check first file is imported if not already loading
        self::assertSame('path/to/file1', $fileLoader->import('my_resource'));

        // Check second file is imported if first is already loading
        $fileLoader->addLoading('path/to/file1');
        self::assertSame('path/to/file2', $fileLoader->import('my_resource'));

        // Check exception throws if first (and only available) file is already loading
        try {
            $fileLoader->import('my_resource');
            self::fail('->import() throws a FileLoaderImportCircularReferenceException if the resource is already loading');
        } catch (\Exception $e) {
            self::assertInstanceOf('Symfony\Component\Config\Exception\FileLoaderImportCircularReferenceException', $e, '->import() throws a FileLoaderImportCircularReferenceException if the resource is already loading');
        }

        // Check exception throws if all files are already loading
        try {
            $fileLoader->addLoading('path/to/file2');
            $fileLoader->import('my_resource');
            self::fail('->import() throws a FileLoaderImportCircularReferenceException if the resource is already loading');
        } catch (\Exception $e) {
            self::assertInstanceOf('Symfony\Component\Config\Exception\FileLoaderImportCircularReferenceException', $e, '->import() throws a FileLoaderImportCircularReferenceException if the resource is already loading');
        }
    }
}

class TestFileLoader extends FileLoader
{
    private $supports = true;

    public function load($resource, $type = null)
    {
        return $resource;
    }

    public function supports($resource, $type = null)
    {
        return $this->supports;
    }

    public function addLoading($resource): void
    {
        self::$loading[$resource] = true;
    }

    public function removeLoading($resource): void
    {
        unset(self::$loading[$resource]);
    }

    public function clearLoading(): void
    {
        self::$loading = [];
    }

    public function setSupports($supports): void
    {
        $this->supports = $supports;
    }
}
