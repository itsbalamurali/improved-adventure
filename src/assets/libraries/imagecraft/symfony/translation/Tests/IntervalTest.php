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
use Symfony\Component\Translation\Interval;

/**
 * @internal
 *
 * @coversNothing
 */
final class IntervalTest extends TestCase
{
    /**
     * @dataProvider provideTestCases
     *
     * @param mixed $expected
     * @param mixed $number
     * @param mixed $interval
     */
    public function testTest($expected, $number, $interval): void
    {
        self::assertSame($expected, Interval::test($number, $interval));
    }

    public function testTestException(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        Interval::test(1, 'foobar');
    }

    public static function provideTestCases(): iterable
    {
        return [
            [true, 3, '{1,2, 3 ,4}'],
            [false, 10, '{1,2, 3 ,4}'],
            [false, 3, '[1,2]'],
            [true, 1, '[1,2]'],
            [true, 2, '[1,2]'],
            [false, 1, ']1,2['],
            [false, 2, ']1,2['],
            [true, log(0), '[-Inf,2['],
            [true, -log(0), '[-2,+Inf]'],
        ];
    }
}
