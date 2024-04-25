<?php



/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\EventDispatcher\Tests;

use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\EventDispatcher\EventDispatcher;

/**
 * Test class for Event.
 *
 * @internal
 *
 * @coversNothing
 */
final class EventTest extends TestCase
{
    /**
     * @var Event
     */
    protected $event;

    /**
     * @var EventDispatcher
     */
    protected $dispatcher;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp(): void
    {
        $this->event = new Event();
        $this->dispatcher = new EventDispatcher();
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown(): void
    {
        $this->event = null;
        $this->dispatcher = null;
    }

    public function testIsPropagationStopped(): void
    {
        self::assertFalse($this->event->isPropagationStopped());
    }

    public function testStopPropagationAndIsPropagationStopped(): void
    {
        $this->event->stopPropagation();
        self::assertTrue($this->event->isPropagationStopped());
    }

    /**
     * @group legacy
     */
    public function testLegacySetDispatcher(): void
    {
        $this->event->setDispatcher($this->dispatcher);
        self::assertSame($this->dispatcher, $this->event->getDispatcher());
    }

    /**
     * @group legacy
     */
    public function testLegacyGetDispatcher(): void
    {
        self::assertNull($this->event->getDispatcher());
    }

    /**
     * @group legacy
     */
    public function testLegacyGetName(): void
    {
        self::assertNull($this->event->getName());
    }

    /**
     * @group legacy
     */
    public function testLegacySetName(): void
    {
        $this->event->setName('foo');
        self::assertSame('foo', $this->event->getName());
    }
}
