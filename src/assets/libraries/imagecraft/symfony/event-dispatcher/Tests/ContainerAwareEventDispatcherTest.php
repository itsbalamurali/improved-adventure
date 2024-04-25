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

use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\Scope;
use Symfony\Component\EventDispatcher\ContainerAwareEventDispatcher;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * @internal
 *
 * @coversNothing
 */
final class ContainerAwareEventDispatcherTest extends AbstractEventDispatcherTest
{
    public function testAddAListenerService(): void
    {
        $event = new Event();

        $service = $this->getMockBuilder('Symfony\Component\EventDispatcher\Tests\Service')->getMock();

        $service
            ->expects(self::once())
            ->method('onEvent')
            ->with($event)
        ;

        $container = new Container();
        $container->set('service.listener', $service);

        $dispatcher = new ContainerAwareEventDispatcher($container);
        $dispatcher->addListenerService('onEvent', ['service.listener', 'onEvent']);

        $dispatcher->dispatch('onEvent', $event);
    }

    public function testAddASubscriberService(): void
    {
        $event = new Event();

        $service = $this->getMockBuilder('Symfony\Component\EventDispatcher\Tests\SubscriberService')->getMock();

        $service
            ->expects(self::once())
            ->method('onEvent')
            ->with($event)
        ;

        $service
            ->expects(self::once())
            ->method('onEventWithPriority')
            ->with($event)
        ;

        $service
            ->expects(self::once())
            ->method('onEventNested')
            ->with($event)
        ;

        $container = new Container();
        $container->set('service.subscriber', $service);

        $dispatcher = new ContainerAwareEventDispatcher($container);
        $dispatcher->addSubscriberService('service.subscriber', 'Symfony\Component\EventDispatcher\Tests\SubscriberService');

        $dispatcher->dispatch('onEvent', $event);
        $dispatcher->dispatch('onEventWithPriority', $event);
        $dispatcher->dispatch('onEventNested', $event);
    }

    public function testPreventDuplicateListenerService(): void
    {
        $event = new Event();

        $service = $this->getMockBuilder('Symfony\Component\EventDispatcher\Tests\Service')->getMock();

        $service
            ->expects(self::once())
            ->method('onEvent')
            ->with($event)
        ;

        $container = new Container();
        $container->set('service.listener', $service);

        $dispatcher = new ContainerAwareEventDispatcher($container);
        $dispatcher->addListenerService('onEvent', ['service.listener', 'onEvent'], 5);
        $dispatcher->addListenerService('onEvent', ['service.listener', 'onEvent'], 10);

        $dispatcher->dispatch('onEvent', $event);
    }

    /**
     * @group legacy
     */
    public function testTriggerAListenerServiceOutOfScope(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $service = $this->getMockBuilder('Symfony\Component\EventDispatcher\Tests\Service')->getMock();

        $scope = new Scope('scope');
        $container = new Container();
        $container->addScope($scope);
        $container->enterScope('scope');

        $container->set('service.listener', $service, 'scope');

        $dispatcher = new ContainerAwareEventDispatcher($container);
        $dispatcher->addListenerService('onEvent', ['service.listener', 'onEvent']);

        $container->leaveScope('scope');
        $dispatcher->dispatch('onEvent');
    }

    /**
     * @group legacy
     */
    public function testReEnteringAScope(): void
    {
        $event = new Event();

        $service1 = $this->getMockBuilder('Symfony\Component\EventDispatcher\Tests\Service')->getMock();

        $service1
            ->expects(self::exactly(2))
            ->method('onEvent')
            ->with($event)
        ;

        $scope = new Scope('scope');
        $container = new Container();
        $container->addScope($scope);
        $container->enterScope('scope');

        $container->set('service.listener', $service1, 'scope');

        $dispatcher = new ContainerAwareEventDispatcher($container);
        $dispatcher->addListenerService('onEvent', ['service.listener', 'onEvent']);
        $dispatcher->dispatch('onEvent', $event);

        $service2 = $this->getMockBuilder('Symfony\Component\EventDispatcher\Tests\Service')->getMock();

        $service2
            ->expects(self::once())
            ->method('onEvent')
            ->with($event)
        ;

        $container->enterScope('scope');
        $container->set('service.listener', $service2, 'scope');

        $dispatcher->dispatch('onEvent', $event);

        $container->leaveScope('scope');

        $dispatcher->dispatch('onEvent');
    }

    public function testHasListenersOnLazyLoad(): void
    {
        $event = new Event();

        $service = $this->getMockBuilder('Symfony\Component\EventDispatcher\Tests\Service')->getMock();

        $container = new Container();
        $container->set('service.listener', $service);

        $dispatcher = new ContainerAwareEventDispatcher($container);
        $dispatcher->addListenerService('onEvent', ['service.listener', 'onEvent']);

        $event->setDispatcher($dispatcher);
        $event->setName('onEvent');

        $service
            ->expects(self::once())
            ->method('onEvent')
            ->with($event)
        ;

        self::assertTrue($dispatcher->hasListeners());

        if ($dispatcher->hasListeners('onEvent')) {
            $dispatcher->dispatch('onEvent');
        }
    }

    public function testGetListenersOnLazyLoad(): void
    {
        $service = $this->getMockBuilder('Symfony\Component\EventDispatcher\Tests\Service')->getMock();

        $container = new Container();
        $container->set('service.listener', $service);

        $dispatcher = new ContainerAwareEventDispatcher($container);
        $dispatcher->addListenerService('onEvent', ['service.listener', 'onEvent']);

        $listeners = $dispatcher->getListeners();

        self::assertArrayHasKey('onEvent', $listeners);

        self::assertCount(1, $dispatcher->getListeners('onEvent'));
    }

    public function testRemoveAfterDispatch(): void
    {
        $service = $this->getMockBuilder('Symfony\Component\EventDispatcher\Tests\Service')->getMock();

        $container = new Container();
        $container->set('service.listener', $service);

        $dispatcher = new ContainerAwareEventDispatcher($container);
        $dispatcher->addListenerService('onEvent', ['service.listener', 'onEvent']);

        $dispatcher->dispatch('onEvent', new Event());
        $dispatcher->removeListener('onEvent', [$container->get('service.listener'), 'onEvent']);
        self::assertFalse($dispatcher->hasListeners('onEvent'));
    }

    public function testRemoveBeforeDispatch(): void
    {
        $service = $this->getMockBuilder('Symfony\Component\EventDispatcher\Tests\Service')->getMock();

        $container = new Container();
        $container->set('service.listener', $service);

        $dispatcher = new ContainerAwareEventDispatcher($container);
        $dispatcher->addListenerService('onEvent', ['service.listener', 'onEvent']);

        $dispatcher->removeListener('onEvent', [$container->get('service.listener'), 'onEvent']);
        self::assertFalse($dispatcher->hasListeners('onEvent'));
    }

    protected function createEventDispatcher()
    {
        $container = new Container();

        return new ContainerAwareEventDispatcher($container);
    }
}

class Service
{
    public function onEvent(Event $e): void {}
}

class SubscriberService implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return [
            'onEvent' => 'onEvent',
            'onEventWithPriority' => ['onEventWithPriority', 10],
            'onEventNested' => [['onEventNested']],
        ];
    }

    public function onEvent(Event $e): void {}

    public function onEventWithPriority(Event $e): void {}

    public function onEventNested(Event $e): void {}
}
