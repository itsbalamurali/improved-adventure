<?php





namespace Facebook\PersistentData;

/**
 * Class FacebookMemoryPersistentDataHandler.
 */
class FacebookMemoryPersistentDataHandler implements PersistentDataInterface
{
    /**
     * @var array the session data to keep in memory
     */
    protected $sessionData = [];

    public function get($key)
    {
        return $this->sessionData[$key] ?? null;
    }

    public function set($key, $value): void
    {
        $this->sessionData[$key] = $value;
    }
}
