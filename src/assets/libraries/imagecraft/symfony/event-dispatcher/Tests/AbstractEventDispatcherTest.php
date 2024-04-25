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
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

abstract class AbstractEventDispatcherTest extends TestCase
{
    // Some pseudo events
    public const preFoo = 'pre.foo';
    public const postFoo = 'post.foo';
    public const preBar = 'pre.bar';
    public const postBar = 'post.bar';

    /**
     * @var EventDispatcher
     */
    private $dispatcher;

    private $listener;

    protected function setUp(): void
    {
        $this->dispatcher = $this->createEventDispatcher();
        $this->listener = new TestEventListener();
    }

    protected function tearDown(): void
    {
        $this->dispatcher = null;
        $this->listener = null;
    }

    public function testInitialState(): void
    {
        self::assertSame([], $this->dispatcher->getListeners());
        self::assertFalse($this->dispatcher->hasListeners(self::preFoo));
        self::assertFalse($this->dispatcher->hasListeners(self::postFoo));
    }

    public function testAddListener(): void
    {
        $this->dispatcher->addListener('pre.foo', [$this->listener, 'preFoo']);
        $this->dispatcher->addListener('post.foo', [$this->listener, 'postFoo']);
        self::assertTrue($this->dispatcher->hasListeners());
        self::assertTrue($this->dispatcher->hasListeners(self::preFoo));
        self::assertTrue($this->dispatcher->hasListeners(self::postFoo));
        self::assertCount(1, $this->dispatcher->getListeners(self::preFoo));
        self::assertCount(1, $this->dispatcher->getListeners(self::postFoo));
        self::assertCount(2, $this->dispatcher->getListeners());
    }

    public function testGetListenersSortsByPriority(): void
    {
        $listener1 = new TestEventListener();
        $listener2 = new TestEventListener();
        $listener3 = new TestEventListener();
        $listener1->name = '1';
        $listener2->name = '2';
        $listener3->name = '3';

        $this->dispatcher->addListener('pre.foo', [$listener1, 'preFoo'], -10);
        $this->dispatcher->addListener('pre.foo', [$listener2, 'preFoo'], 10);
        $this->dispatcher->addListener('pre.foo', [$listener3, 'preFoo']);

        $expected = [
            [$listener2, 'preFoo'],
            [$listener3, 'preFoo'],
            [$listener1, 'preFoo'],
        ];

        self::assertSame($expected, $this->dispatcher->getListeners('pre.foo'));
    }

    public function testGetAllListenersSortsByPriority(): void
    {
        $listener1 = new TestEventListener();
        $listener2 = new TestEventListener();
        $listener3 = new TestEventListener();
        $listener4 = new TestEventListener();
        $listener5 = new TestEventListener();
        $listener6 = new TestEventListener();

        $this->dispatcher->addListener('pre.foo', $listener1, -10);
        $this->dispatcher->addListener('pre.foo', $listener2);
        $this->dispatcher->addListener('pre.foo', $listener3, 10);
        $this->dispatcher->addListener('post.foo', $listener4, -10);
        $this->dispatcher->addListener('post.foo', $listener5);
        $this->dispatcher->addListener('post.foo', $listener6, 10);

        $expected = [
            'pre.foo' => [$listener3, $listener2, $listener1],
            'post.foo' => [$listener6, $listener5, $listener4],
        ];

        self::assertSame($expected, $this->dispatcher->getListeners());
    }

    public function testGetListenerPriority(): void
    {
        $listener1 = new TestEventListener();
        $listener2 = new TestEventListener();

        $this->dispatcher->addListener('pre.foo', $listener1, -10);
        $this->dispatcher->addListener('pre.foo', $listener2);

        self::assertSame(-10, $this->dispatcher->getListenerPriority('pre.foo', $listener1));
        self::assertSame(0, $this->dispatcher->getListenerPriority('pre.foo', $listener2));
        self::assertNull($this->dispatcher->getListenerPriority('pre.bar', $listener2));
        self::assertNull($this->dispatcher->getListenerPriority('pre.foo', static function (): void {}));
    }

    public function testDispatch(): void
    {
        $this->dispatcher->addListener('pre.foo', [$this->listener, 'preFoo']);
        $this->dispatcher->addListener('post.foo', [$this->listener, 'postFoo']);
        $this->dispatcher->dispatch(self::preFoo);
        self::assertTrue($this->listener->preFooInvoked);
        self::assertFalse($this->listener->postFooInvoked);
        self::assertInstanceOf('Symfony\Component\EventDispatcher\Event', $this->dispatcher->dispatch('noevent'));
        self::assertInstanceOf('Symfony\Component\EventDispatcher\Event', $this->dispatcher->dispatch(self::preFoo));
        $event = new Event();
        $return = $this->dispatcher->dispatch(self::preFoo, $event);
        self::assertSame($event, $return);
    }

    /**
     * @group legacy
     */
    public function testLegacyDispatch(): void
    {
        $event = new Event();
        $this->dispatcher->dispatch(self::preFoo, $event);
        self::assertSame('pre.foo', $event->getName());
    }

    public function testDispatchForClosure(): void
    {
        $invoked = 0;
        $listener = static function () use (&$invoked): void {
            ++$invoked;
        };
        $this->dispatcher->addListener('pre.foo', $listener);
        $this->dispatcher->addListener('post.foo', $listener);
        $this->dispatcher->dispatch(self::preFoo);
        self::assertSame(1, $invoked);
    }

    public function testStopEventPropagation(): void
    {
        $otherListener = new TestEventListener();

        // postFoo() stops the propagation, so only one listener should
        // be executed
        // Manually set priority to enforce $this->listener to be called first
        $this->dispatcher->addListener('post.foo', [$this->listener, 'postFoo'], 10);
        $this->dispatcher->addListener('post.foo', [$otherListener, 'postFoo']);
        $this->dispatcher->dispatch(self::postFoo);
        self::assertTrue($this->listener->postFooInvoked);
        self::assertFalse($otherListener->postFooInvoked);
    }

    public function testDispatchByPriority(): void
    {
        $invoked = [];
        $listener1 = static function () use (&$invoked): void {
            $invoked[] = '1';
        };
        $listener2 = static function () use (&$invoked): void {
            $invoked[] = '2';
        };
        $listener3 = static function () use (&$invoked): void {
            $invoked[] = '3';
        };
        $this->dispatcher->addListener('pre.foo', $listener1, -10);
        $this->dispatcher->addListener('pre.foo', $listener2);
        $this->dispatcher->addListener('pre.foo', $listener3, 10);
        $this->dispatcher->dispatch(self::preFoo);
        self::assertSame(['3', '2', '1'], $invoked);
    }

    public function testRemoveListener(): void
    {
        $this->dispatcher->addListener('pre.bar', $this->listener);
        self::assertTrue($this->dispatcher->hasListeners(self::preBar));
        $this->dispatcher->removeListener('pre.bar', $this->listener);
        self::assertFalse($this->dispatcher->hasListeners(self::preBar));
        $this->dispatcher->removeListener('notExists', $this->listener);
    }

    public function testAddSubscriber(): void
    {
        $eventSubscriber = new TestEventSubscriber();
        $this->dispatcher->addSubscriber($eventSubscriber);
        self::assertTrue($this->dispatcher->hasListeners(self::preFoo));
        self::assertTrue($this->dispatcher->hasListeners(self::postFoo));
    }

    public function testAddSubscriberWithPriorities(): void
    {
        $eventSubscriber = new TestEventSubscriber();
        $this->dispatcher->addSubscriber($eventSubscriber);

        $eventSubscriber = new TestEventSubscriberWithPriorities();
        $this->dispatcher->addSubscriber($eventSubscriber);

        $listeners = $this->dispatcher->getListeners('pre.foo');
        self::assertTrue($this->dispatcher->hasListeners(self::preFoo));
        self::assertCount(2, $listeners);
        self::assertInstanceOf('Symfony\Component\EventDispatcher\Tests\TestEventSubscriberWithPriorities', $listeners[0][0]);
    }

    public function testAddSubscriberWithMultipleListeners(): void
    {
        $eventSubscriber = new TestEventSubscriberWithMultipleListeners();
        $this->dispatcher->addSubscriber($eventSubscriber);

        $listeners = $this->dispatcher->getListeners('pre.foo');
        self::assertTrue($this->dispatcher->hasListeners(self::preFoo));
        self::assertCount(2, $listeners);
        self::assertSame('preFoo2', $listeners[0][1]);
    }

    public function testRemoveSubscriber(): void
    {
        $eventSubscriber = new TestEventSubscriber();
        $this->dispatcher->addSubscriber($eventSubscriber);
        self::assertTrue($this->dispatcher->hasListeners(self::preFoo));
        self::assertTrue($this->dispatcher->hasListeners(self::postFoo));
        $this->dispatcher->removeSubscriber($eventSubscriber);
        self::assertFalse($this->dispatcher->hasListeners(self::preFoo));
        self::assertFalse($this->dispatcher->hasListeners(self::postFoo));
    }

    public function testRemoveSubscriberWithPriorities(): void
    {
        $eventSubscriber = new TestEventSubscriberWithPriorities();
        $this->dispatcher->addSubscriber($eventSubscriber);
        self::assertTrue($this->dispatcher->hasListeners(self::preFoo));
        $this->dispatcher->removeSubscriber($eventSubscriber);
        self::assertFalse($this->dispatcher->hasListeners(self::preFoo));
    }

    public function testRemoveSubscriberWithMultipleListeners(): void
    {
        $eventSubscriber = new TestEventSubscriberWithMultipleListeners();
        $this->dispatcher->addSubscriber($eventSubscriber);
        self::assertTrue($this->dispatcher->hasListeners(self::preFoo));
        self::assertCount(2, $this->dispatcher->getListeners(self::preFoo));
        $this->dispatcher->removeSubscriber($eventSubscriber);
        self::assertFalse($this->dispatcher->hasListeners(self::preFoo));
    }

    /**
     * @group legacy
     */
    public function testLegacyEventReceivesTheDispatcherInstance(): void
    {
        $dispatcher = null;
        $this->dispatcher->addListener('test', static function ($event) use (&$dispatcher): void {
            $dispatcher = $event->getDispatcher();
        });
        $this->dispatcher->dispatch('test');
        self::assertSame($this->dispatcher, $dispatcher);
    }

    public function testEventReceivesTheDispatcherInstanceAsArgument(): void
    {
        $listener = new TestWithDispatcher();
        $this->dispatcher->addListener('test', [$listener, 'foo']);
        self::assertNull($listener->name);
        self::assertNull($listener->dispatcher);
        $this->dispatcher->dispatch('test');
        self::assertSame('test', $listener->name);
        self::assertSame($this->dispatcher, $listener->dispatcher);
    }

    /**
     * @see https://bugs.php.net/bug.php?id=62976
     *
     * This bug affects:
     *  - The PHP 5.3 branch for versions < 5.3.18
     *  - The PHP 5.4 branch for versions < 5.4.8
     *  - The PHP 5.5 branch is not affected
     */
    public function testWorkaroundForPhpBug62976(): void
    {
        $dispatcher = $this->createEventDispatcher();
        $dispatcher->addListener('bug.62976', new CallableClass());
        $dispatcher->removeListener('bug.62976', static function (): void {});
        self::assertTrue($dispatcher->hasListeners('bug.62976'));
    }

    public function testHasListenersWhenAddedCallbackListenerIsRemoved(): void
    {
        $listener = static function (): void {};
        $this->dispatcher->addListener('foo', $listener);
        $this->dispatcher->removeListener('foo', $listener);
        self::assertFalse($this->dispatcher->hasListeners());
    }

    public function testGetListenersWhenAddedCallbackListenerIsRemoved(): void
    {
        $listener = static function (): void {};
        $this->dispatcher->addListener('foo', $listener);
        $this->dispatcher->removeListener('foo', $listener);
        self::assertSame([], $this->dispatcher->getListeners());
    }

    public function testHasListenersWithoutEventsReturnsFalseAfterHasListenersWithEventHasBeenCalled(): void
    {
        self::assertFalse($this->dispatcher->hasListeners('foo'));
        self::assertFalse($this->dispatcher->hasListeners());
    }

    abstract protected function createEventDispatcher();
}

class CallableClass
{
    public function __invoke(): void {}
}

class TestEventListener
{
    public $preFooInvoked = false;
    public $postFooInvoked = false;

    // Listener methods

    public function preFoo(Event $e): void
    {
        $this->preFooInvoked = true;
    }

    public function postFoo(Event $e): void
    {
        $this->postFooInvoked = true;

        $e->stopPropagation();
    }
}

class TestWithDispatcher
{
    public $name;
    public $dispatcher;

    public function foo(Event $e, $name, $dispatcher): void
    {
        $this->name = $name;
        $this->dispatcher = $dispatcher;
    }
}

class TestEventSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return ['pre.foo' => 'preFoo', 'post.foo' => 'postFoo'];
    }
}

class TestEventSubscriberWithPriorities implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return [
            'pre.foo' => ['preFoo', 10],
            'post.foo' => ['postFoo'],
        ];
    }
}

class TestEventSubscriberWithMultipleListeners implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return ['pre.foo' => [
            ['preFoo1'],
            ['preFoo2', 10],
        ]];
    }
}
