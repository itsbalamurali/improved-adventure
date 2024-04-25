<?php





namespace Facebook\PersistentData;

use Facebook\Exceptions\FacebookSDKException;

/**
 * Class FacebookSessionPersistentDataHandler.
 */
class FacebookSessionPersistentDataHandler implements PersistentDataInterface
{
    /**
     * @var string prefix to use for session variables
     */
    protected $sessionPrefix = 'FBRLH_';

    /**
     * Init the session handler.
     *
     * @param bool $enableSessionCheck
     *
     * @throws FacebookSDKException
     */
    public function __construct($enableSessionCheck = true)
    {
        if ($enableSessionCheck && PHP_SESSION_ACTIVE !== session_status()) {
            throw new FacebookSDKException(
                'Sessions are not active. Please make sure session_start() is at the top of your script.',
                720
            );
        }
    }

    public function get($key)
    {
        if (isset($_SESSION[$this->sessionPrefix.$key])) {
            return $_SESSION[$this->sessionPrefix.$key];
        }

        return null;
    }

    public function set($key, $value): void
    {
        $_SESSION[$this->sessionPrefix.$key] = $value;
    }
}
