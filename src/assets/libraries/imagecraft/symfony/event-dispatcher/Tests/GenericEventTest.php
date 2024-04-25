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
use Symfony\Component\EventDispatcher\GenericEvent;

/**
 * Test class for Event.
 *
 * @internal
 *
 * @coversNothing
 */
final class GenericEventTest extends TestCase
{
    /**
     * @var GenericEvent
     */
    private $event;

    private $subject;

    /**
     * Prepares the environment before running a test.
     */
    protected function setUp(): void
    {
        $this->subject = new \stdClass();
        $this->event = new GenericEvent($this->subject, ['name' => 'Event']);
    }

    /**
     * Cleans up the environment after running a test.
     */
    protected function tearDown(): void
    {
        $this->subject = null;
        $this->event = null;
    }

    public function testConstruct(): void
    {
        self::assertSame($this->event, new GenericEvent($this->subject, ['name' => 'Event']));
    }

    /**
     * Tests Event->getArgs().
     */
    public function testGetArguments(): void
    {
        // test getting all
        self::assertSame(['name' => 'Event'], $this->event->getArguments());
    }

    public function testSetArguments(): void
    {
        $result = $this->event->setArguments(['foo' => 'bar']);
        self::assertAttributeSame(['foo' => 'bar'], 'arguments', $this->event);
        self::assertSame($this->event, $result);
    }

    public function testSetArgument(): void
    {
        $result = $this->event->setArgument('foo2', 'bar2');
        self::assertAttributeSame(['name' => 'Event', 'foo2' => 'bar2'], 'arguments', $this->event);
        self::assertSame($this->event, $result);
    }

    public function testGetArgument(): void
    {
        // test getting key
        self::assertSame('Event', $this->event->getArgument('name'));
    }

    public function testGetArgException(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->event->getArgument('nameNotExist');
    }

    public function testOffsetGet(): void
    {
        // test getting key
        self::assertSame('Event', $this->event['name']);

        // test getting invalid arg
        $this->{method_exists($this, $_ = 'expectException') ? $_ : 'setExpectedException'}('InvalidArgumentException');
        self::assertFalse($this->event['nameNotExist']);
    }

    public function testOffsetSet(): void
    {
        $this->event['foo2'] = 'bar2';
        self::assertAttributeSame(['name' => 'Event', 'foo2' => 'bar2'], 'arguments', $this->event);
    }

    public function testOffsetUnset(): void
    {
        unset($this->event['name']);
        self::assertAttributeSame([], 'arguments', $this->event);
    }

    public function testOffsetIsset(): void
    {
        self::assertArrayHasKey('name', $this->event);
        self::assertArrayNotHasKey('nameNotExist', $this->event);
    }

    public function testHasArgument(): void
    {
        self::assertTrue($this->event->hasArgument('name'));
        self::assertFalse($this->event->hasArgument('nameNotExist'));
    }

    public function testGetSubject(): void
    {
        self::assertSame($this->subject, $this->event->getSubject());
    }

    public function testHasIterator(): void
    {
        $data = [];
        foreach ($this->event as $key => $value) {
            $data[$key] = $value;
        }
        self::assertSame(['name' => 'Event'], $data);
    }
}
