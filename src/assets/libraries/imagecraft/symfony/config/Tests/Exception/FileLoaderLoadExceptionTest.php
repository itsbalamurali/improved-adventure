<?php



/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Config\Tests\Exception;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\Exception\FileLoaderLoadException;

/**
 * @internal
 *
 * @coversNothing
 */
final class FileLoaderLoadExceptionTest extends TestCase
{
    public function testMessageCannotLoadResource(): void
    {
        $exception = new FileLoaderLoadException('resource', null);
        self::assertSame('Cannot load resource "resource".', $exception->getMessage());
    }

    public function testMessageCannotImportResourceFromSource(): void
    {
        $exception = new FileLoaderLoadException('resource', 'sourceResource');
        self::assertSame('Cannot import resource "resource" from "sourceResource".', $exception->getMessage());
    }

    public function testMessageCannotImportBundleResource(): void
    {
        $exception = new FileLoaderLoadException('@resource', 'sourceResource');
        self::assertSame(
            'Cannot import resource "@resource" from "sourceResource". '.
            'Make sure the "resource" bundle is correctly registered and loaded in the application kernel class. '.
            'If the bundle is registered, make sure the bundle path "@resource" is not empty.',
            $exception->getMessage()
        );
    }

    public function testMessageHasPreviousErrorWithDotAndUnableToLoad(): void
    {
        $exception = new FileLoaderLoadException(
            'resource',
            null,
            null,
            new \Exception('There was a previous error with an ending dot.')
        );
        self::assertSame(
            'There was a previous error with an ending dot in resource (which is loaded in resource "resource").',
            $exception->getMessage()
        );
    }

    public function testMessageHasPreviousErrorWithoutDotAndUnableToLoad(): void
    {
        $exception = new FileLoaderLoadException(
            'resource',
            null,
            null,
            new \Exception('There was a previous error with no ending dot')
        );
        self::assertSame(
            'There was a previous error with no ending dot in resource (which is loaded in resource "resource").',
            $exception->getMessage()
        );
    }

    public function testMessageHasPreviousErrorAndUnableToLoadBundle(): void
    {
        $exception = new FileLoaderLoadException(
            '@resource',
            null,
            null,
            new \Exception('There was a previous error with an ending dot.')
        );
        self::assertSame(
            'There was a previous error with an ending dot in @resource '.
            '(which is loaded in resource "@resource"). '.
            'Make sure the "resource" bundle is correctly registered and loaded in the application kernel class. '.
            'If the bundle is registered, make sure the bundle path "@resource" is not empty.',
            $exception->getMessage()
        );
    }
}
