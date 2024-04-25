<?php



/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\EventDispatcher\Tests\Debug;

use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\Debug\TraceableEventDispatcher;
use Symfony\Component\EventDispatcher\Debug\WrappedListener;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Stopwatch\Stopwatch;

/**
 * @internal
 *
 * @coversNothing
 */
final class TraceableEventDispatcherTest extends TestCase
{
    public function testAddRemoveListener(): void
    {
        $dispatcher = new EventDispatcher();
        $tdispatcher = new TraceableEventDispatcher($dispatcher, new Stopwatch());

        $tdispatcher->addListener('foo', $listener = static function (): void {});
        $listeners = $dispatcher->getListeners('foo');
        self::assertCount(1, $listeners);
        self::assertSame($listener, $listeners[0]);

        $tdispatcher->removeListener('foo', $listener);
        self::assertCount(0, $dispatcher->getListeners('foo'));
    }

    public function testGetListeners(): void
    {
        $dispatcher = new EventDispatcher();
        $tdispatcher = new TraceableEventDispatcher($dispatcher, new Stopwatch());

        $tdispatcher->addListener('foo', $listener = static function (): void {});
        self::assertSame($dispatcher->getListeners('foo'), $tdispatcher->getListeners('foo'));
    }

    public function testHasListeners(): void
    {
        $dispatcher = new EventDispatcher();
        $tdispatcher = new TraceableEventDispatcher($dispatcher, new Stopwatch());

        self::assertFalse($dispatcher->hasListeners('foo'));
        self::assertFalse($tdispatcher->hasListeners('foo'));

        $tdispatcher->addListener('foo', $listener = static function (): void {});
        self::assertTrue($dispatcher->hasListeners('foo'));
        self::assertTrue($tdispatcher->hasListeners('foo'));
    }

    public function testGetListenerPriority(): void
    {
        $dispatcher = new EventDispatcher();
        $tdispatcher = new TraceableEventDispatcher($dispatcher, new Stopwatch());

        $tdispatcher->addListener('foo', static function (): void {}, 123);

        $listeners = $dispatcher->getListeners('foo');
        self::assertSame(123, $tdispatcher->getListenerPriority('foo', $listeners[0]));

        // Verify that priority is preserved when listener is removed and re-added
        // in preProcess() and postProcess().
        $tdispatcher->dispatch('foo', new Event());
        $listeners = $dispatcher->getListeners('foo');
        self::assertSame(123, $tdispatcher->getListenerPriority('foo', $listeners[0]));
    }

    public function testGetListenerPriorityReturnsZeroWhenWrappedMethodDoesNotExist(): void
    {
        $dispatcher = $this->getMockBuilder('Symfony\Component\EventDispatcher\EventDispatcherInterface')->getMock();
        $traceableEventDispatcher = new TraceableEventDispatcher($dispatcher, new Stopwatch());
        $traceableEventDispatcher->addListener('foo', static function (): void {}, 123);
        $listeners = $traceableEventDispatcher->getListeners('foo');

        self::assertSame(0, $traceableEventDispatcher->getListenerPriority('foo', $listeners[0]));
    }

    public function testAddRemoveSubscriber(): void
    {
        $dispatcher = new EventDispatcher();
        $tdispatcher = new TraceableEventDispatcher($dispatcher, new Stopwatch());

        $subscriber = new EventSubscriber();

        $tdispatcher->addSubscriber($subscriber);
        $listeners = $dispatcher->getListeners('foo');
        self::assertCount(1, $listeners);
        self::assertSame([$subscriber, 'call'], $listeners[0]);

        $tdispatcher->removeSubscriber($subscriber);
        self::assertCount(0, $dispatcher->getListeners('foo'));
    }

    /**
     * @dataProvider provideGetCalledListenersCases
     *
     * @param bool $isWrapped
     */
    public function testGetCalledListeners($isWrapped): void
    {
        $dispatcher = new EventDispatcher();
        $stopWatch = new Stopwatch();
        $tdispatcher = new TraceableEventDispatcher($dispatcher, $stopWatch);

        $listener = static function (): void {};
        if ($isWrapped) {
            $listener = new WrappedListener($listener, 'foo', $stopWatch, $dispatcher);
        }

        $tdispatcher->addListener('foo', $listener, 5);

        self::assertSame([], $tdispatcher->getCalledListeners());
        self::assertSame(['foo.closure' => ['event' => 'foo', 'type' => 'Closure', 'pretty' => 'closure', 'priority' => 5]], $tdispatcher->getNotCalledListeners());

        $tdispatcher->dispatch('foo');

        self::assertSame(['foo.closure' => ['event' => 'foo', 'type' => 'Closure', 'pretty' => 'closure', 'priority' => 5]], $tdispatcher->getCalledListeners());
        self::assertSame([], $tdispatcher->getNotCalledListeners());
    }

    public static function provideGetCalledListenersCases(): iterable
    {
        return [
            [false],
            [true],
        ];
    }

    public function testGetCalledListenersNested(): void
    {
        $tdispatcher = null;
        $dispatcher = new TraceableEventDispatcher(new EventDispatcher(), new Stopwatch());
        $dispatcher->addListener('foo', static function (Event $event, $eventName, $dispatcher) use (&$tdispatcher): void {
            $tdispatcher = $dispatcher;
            $dispatcher->dispatch('bar');
        });
        $dispatcher->addListener('bar', static function (Event $event): void {});
        $dispatcher->dispatch('foo');
        self::assertSame($dispatcher, $tdispatcher);
        self::assertCount(2, $dispatcher->getCalledListeners());
    }

    public function testLogger(): void
    {
        $logger = $this->getMockBuilder('Psr\Log\LoggerInterface')->getMock();

        $dispatcher = new EventDispatcher();
        $tdispatcher = new TraceableEventDispatcher($dispatcher, new Stopwatch(), $logger);
        $tdispatcher->addListener('foo', $listener1 = static function (): void {});
        $tdispatcher->addListener('foo', $listener2 = static function (): void {});

        $logger->expects(self::at(0))->method('debug')->with('Notified event "foo" to listener "closure".');
        $logger->expects(self::at(1))->method('debug')->with('Notified event "foo" to listener "closure".');

        $tdispatcher->dispatch('foo');
    }

    public function testLoggerWithStoppedEvent(): void
    {
        $logger = $this->getMockBuilder('Psr\Log\LoggerInterface')->getMock();

        $dispatcher = new EventDispatcher();
        $tdispatcher = new TraceableEventDispatcher($dispatcher, new Stopwatch(), $logger);
        $tdispatcher->addListener('foo', $listener1 = static function (Event $event): void { $event->stopPropagation(); });
        $tdispatcher->addListener('foo', $listener2 = static function (): void {});

        $logger->expects(self::at(0))->method('debug')->with('Notified event "foo" to listener "closure".');
        $logger->expects(self::at(1))->method('debug')->with('Listener "closure" stopped propagation of the event "foo".');
        $logger->expects(self::at(2))->method('debug')->with('Listener "closure" was not called for event "foo".');

        $tdispatcher->dispatch('foo');
    }

    public function testDispatchCallListeners(): void
    {
        $called = [];

        $dispatcher = new EventDispatcher();
        $tdispatcher = new TraceableEventDispatcher($dispatcher, new Stopwatch());
        $tdispatcher->addListener('foo', static function () use (&$called): void { $called[] = 'foo1'; }, 10);
        $tdispatcher->addListener('foo', static function () use (&$called): void { $called[] = 'foo2'; }, 20);

        $tdispatcher->dispatch('foo');

        self::assertSame(['foo2', 'foo1'], $called);
    }

    public function testDispatchNested(): void
    {
        $dispatcher = new TraceableEventDispatcher(new EventDispatcher(), new Stopwatch());
        $loop = 1;
        $dispatchedEvents = 0;
        $dispatcher->addListener('foo', $listener1 = static function () use ($dispatcher, &$loop): void {
            ++$loop;
            if (2 === $loop) {
                $dispatcher->dispatch('foo');
            }
        });
        $dispatcher->addListener('foo', static function () use (&$dispatchedEvents): void {
            ++$dispatchedEvents;
        });

        $dispatcher->dispatch('foo');

        self::assertSame(2, $dispatchedEvents);
    }

    public function testDispatchReusedEventNested(): void
    {
        $nestedCall = false;
        $dispatcher = new TraceableEventDispatcher(new EventDispatcher(), new Stopwatch());
        $dispatcher->addListener('foo', static function (Event $e) use ($dispatcher): void {
            $dispatcher->dispatch('bar', $e);
        });
        $dispatcher->addListener('bar', static function (Event $e) use (&$nestedCall): void {
            $nestedCall = true;
        });

        self::assertFalse($nestedCall);
        $dispatcher->dispatch('foo');
        self::assertTrue($nestedCall);
    }

    public function testListenerCanRemoveItselfWhenExecuted(): void
    {
        $eventDispatcher = new TraceableEventDispatcher(new EventDispatcher(), new Stopwatch());
        $listener1 = static function ($event, $eventName, EventDispatcherInterface $dispatcher) use (&$listener1): void {
            $dispatcher->removeListener('foo', $listener1);
        };
        $eventDispatcher->addListener('foo', $listener1);
        $eventDispatcher->addListener('foo', static function (): void {});
        $eventDispatcher->dispatch('foo');

        self::assertCount(1, $eventDispatcher->getListeners('foo'), 'expected listener1 to be removed');
    }
}

class EventSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return ['foo' => 'call'];
    }
}
