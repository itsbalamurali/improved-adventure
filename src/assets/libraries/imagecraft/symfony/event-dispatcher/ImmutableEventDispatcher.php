<?php



/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\EventDispatcher;

/**
 * A read-only proxy for an event dispatcher.
 *
 * @author Bernhard Schussek <bschussek@gmail.com>
 */
class ImmutableEventDispatcher implements EventDispatcherInterface
{
    private $dispatcher;

    public function __construct(EventDispatcherInterface $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    public function dispatch($eventName, ?Event $event = null)
    {
        return $this->dispatcher->dispatch($eventName, $event);
    }

    public function addListener($eventName, $listener, $priority = 0): void
    {
        throw new \BadMethodCallException('Unmodifiable event dispatchers must not be modified.');
    }

    public function addSubscriber(EventSubscriberInterface $subscriber): void
    {
        throw new \BadMethodCallException('Unmodifiable event dispatchers must not be modified.');
    }

    public function removeListener($eventName, $listener): void
    {
        throw new \BadMethodCallException('Unmodifiable event dispatchers must not be modified.');
    }

    public function removeSubscriber(EventSubscriberInterface $subscriber): void
    {
        throw new \BadMethodCallException('Unmodifiable event dispatchers must not be modified.');
    }

    public function getListeners($eventName = null)
    {
        return $this->dispatcher->getListeners($eventName);
    }

    public function getListenerPriority($eventName, $listener)
    {
        return $this->dispatcher->getListenerPriority($eventName, $listener);
    }

    public function hasListeners($eventName = null)
    {
        return $this->dispatcher->hasListeners($eventName);
    }
}
