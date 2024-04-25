<?php



/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Filesystem\Tests;

use Symfony\Component\Filesystem\Exception\FileNotFoundException;
use Symfony\Component\Filesystem\Exception\IOException;

/**
 * Test class for Filesystem.
 *
 * @internal
 *
 * @coversNothing
 */
final class ExceptionTest extends \PHPUnit\Framework\TestCase
{
    public function testGetPath(): void
    {
        $e = new IOException('', 0, null, '/foo');
        self::assertSame('/foo', $e->getPath(), 'The pass should be returned.');
    }

    public function testGeneratedMessage(): void
    {
        $e = new FileNotFoundException(null, 0, null, '/foo');
        self::assertSame('/foo', $e->getPath());
        self::assertSame('File "/foo" could not be found.', $e->getMessage(), 'A message should be generated.');
    }

    public function testGeneratedMessageWithoutPath(): void
    {
        $e = new FileNotFoundException();
        self::assertSame('File could not be found.', $e->getMessage(), 'A message should be generated.');
    }

    public function testCustomMessage(): void
    {
        $e = new FileNotFoundException('bar', 0, null, '/foo');
        self::assertSame('bar', $e->getMessage(), 'A custom message should be possible still.');
    }
}
