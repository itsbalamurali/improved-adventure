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
use Symfony\Component\EventDispatcher\ImmutableEventDispatcher;

/**
 * @author Bernhard Schussek <bschussek@gmail.com>
 *
 * @internal
 *
 * @coversNothing
 */
final class ImmutableEventDispatcherTest extends TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $innerDispatcher;

    /**
     * @var ImmutableEventDispatcher
     */
    private $dispatcher;

    protected function setUp(): void
    {
        $this->innerDispatcher = $this->getMockBuilder('Symfony\Component\EventDispatcher\EventDispatcherInterface')->getMock();
        $this->dispatcher = new ImmutableEventDispatcher($this->innerDispatcher);
    }

    public function testDispatchDelegates(): void
    {
        $event = new Event();

        $this->innerDispatcher->expects(self::once())
            ->method('dispatch')
            ->with('event', $event)
            ->willReturn('result')
        ;

        self::assertSame('result', $this->dispatcher->dispatch('event', $event));
    }

    public function testGetListenersDelegates(): void
    {
        $this->innerDispatcher->expects(self::once())
            ->method('getListeners')
            ->with('event')
            ->willReturn('result')
        ;

        self::assertSame('result', $this->dispatcher->getListeners('event'));
    }

    public function testHasListenersDelegates(): void
    {
        $this->innerDispatcher->expects(self::once())
            ->method('hasListeners')
            ->with('event')
            ->willReturn('result')
        ;

        self::assertSame('result', $this->dispatcher->hasListeners('event'));
    }

    public function testAddListenerDisallowed(): void
    {
        $this->expectException(\BadMethodCallException::class);

        $this->dispatcher->addListener('event', static fn () => 'foo');
    }

    public function testAddSubscriberDisallowed(): void
    {
        $this->expectException(\BadMethodCallException::class);

        $subscriber = $this->getMockBuilder('Symfony\Component\EventDispatcher\EventSubscriberInterface')->getMock();

        $this->dispatcher->addSubscriber($subscriber);
    }

    public function testRemoveListenerDisallowed(): void
    {
        $this->expectException(\BadMethodCallException::class);

        $this->dispatcher->removeListener('event', static fn () => 'foo');
    }

    public function testRemoveSubscriberDisallowed(): void
    {
        $this->expectException(\BadMethodCallException::class);

        $subscriber = $this->getMockBuilder('Symfony\Component\EventDispatcher\EventSubscriberInterface')->getMock();

        $this->dispatcher->removeSubscriber($subscriber);
    }
}
