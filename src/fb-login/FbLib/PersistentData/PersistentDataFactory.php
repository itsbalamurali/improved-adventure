<?php





namespace Facebook\PersistentData;

class PersistentDataFactory
{
    private function __construct()
    {
        // a factory constructor should never be invoked
    }

    /**
     * PersistentData generation.
     *
     * @param null|PersistentDataInterface|string $handler
     *
     * @return PersistentDataInterface
     *
     * @throws \InvalidArgumentException if the persistent data handler isn't "session", "memory", or an instance of Facebook\PersistentData\PersistentDataInterface
     */
    public static function createPersistentDataHandler($handler)
    {
        if (!$handler) {
            return PHP_SESSION_ACTIVE === session_status()
                ? new FacebookSessionPersistentDataHandler()
                : new FacebookMemoryPersistentDataHandler();
        }

        if ($handler instanceof PersistentDataInterface) {
            return $handler;
        }

        if ('session' === $handler) {
            return new FacebookSessionPersistentDataHandler();
        }
        if ('memory' === $handler) {
            return new FacebookMemoryPersistentDataHandler();
        }

        throw new \InvalidArgumentException('The persistent data handler must be set to "session", "memory", or be an instance of Facebook\PersistentData\PersistentDataInterface');
    }
}
