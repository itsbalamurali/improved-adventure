<?php





namespace Facebook;

/**
 * Class FacebookRedirectLoginHelper.
 *
 * @author Fosco Marotto <fjm@fb.com>
 * @author David Poll <depoll@fb.com>
 */
class FacebookRedirectLoginHelper
{
    /**
     * @var string State token for CSRF validation
     */
    protected $state;

    /**
     * @var bool Toggle for PHP session status check
     */
    protected $checkForSessionStatus = true;

    /**
     * @var string The application id
     */
    private $appId;

    /**
     * @var string The application secret
     */
    private $appSecret;

    /**
     * @var string The redirect URL for the application
     */
    private $redirectUrl;

    /**
     * @var string Prefix to use for session variables
     */
    private $sessionPrefix = 'FBRLH_';

    /**
     * Constructs a RedirectLoginHelper for a given appId and redirectUrl.
     *
     * @param string $redirectUrl The URL Facebook should redirect users to
     *                            after login
     * @param string $appId       The application id
     * @param string $appSecret   The application secret
     */
    public function __construct($redirectUrl, $appId = null, $appSecret = null)
    {
        $this->appId = FacebookSession::_getTargetAppId($appId);
        $this->appSecret = FacebookSession::_getTargetAppSecret($appSecret);
        $this->redirectUrl = $redirectUrl;
    }

    /**
     * Stores CSRF state and returns a URL to which the user should be sent to
     *   in order to continue the login process with Facebook.  The
     *   provided redirectUrl should invoke the handleRedirect method.
     *
     * @param array  $scope          List of permissions to request during login
     * @param string $version        Optional Graph API version if not default (v2.0)
     * @param bool   $displayAsPopup Indicate if the page will be displayed as a popup
     *
     * @return string
     */
    public function getLoginUrl($scope = [], $version = null, $displayAsPopup = false)
    {
        $version = ($version ?: FacebookRequest::GRAPH_API_VERSION);
        $this->state = $this->random(16);
        $this->storeState($this->state);
        $params = [
            'client_id' => $this->appId,
            'redirect_uri' => $this->redirectUrl,
            'state' => $this->state,
            'sdk' => 'php-sdk-'.FacebookRequest::VERSION,
            'scope' => implode(',', $scope),
        ];

        if ($displayAsPopup) {
            $params['display'] = 'popup';
        }

        return 'https://www.facebook.com/'.$version.'/dialog/oauth?'.
          http_build_query($params, null, '&');
    }

    /**
     * Returns a URL to which the user should be sent to re-request permissions.
     *
     * @param array  $scope   List of permissions to re-request
     * @param string $version Optional Graph API version if not default (v2.0)
     *
     * @return string
     */
    public function getReRequestUrl($scope = [], $version = null)
    {
        $version = ($version ?: FacebookRequest::GRAPH_API_VERSION);
        $this->state = $this->random(16);
        $this->storeState($this->state);
        $params = [
            'client_id' => $this->appId,
            'redirect_uri' => $this->redirectUrl,
            'state' => $this->state,
            'sdk' => 'php-sdk-'.FacebookRequest::VERSION,
            'auth_type' => 'rerequest',
            'scope' => implode(',', $scope),
        ];

        return 'https://www.facebook.com/'.$version.'/dialog/oauth?'.
          http_build_query($params, null, '&');
    }

    /**
     * Returns the URL to send the user in order to log out of Facebook.
     *
     * @param FacebookSession $session The session that will be logged out
     * @param string          $next    The url Facebook should redirect the user to after
     *                                 a successful logout
     *
     * @return string
     *
     * @throws FacebookSDKException
     */
    public function getLogoutUrl(FacebookSession $session, $next)
    {
        if ($session->getAccessToken()->isAppSession()) {
            throw new FacebookSDKException(
                'Cannot generate a Logout URL with an App Session.',
                722
            );
        }
        $params = [
            'next' => $next,
            'access_token' => $session->getToken(),
        ];

        return 'https://www.facebook.com/logout.php?'.http_build_query($params, null, '&');
    }

    /**
     * Handles a response from Facebook, including a CSRF check, and returns a
     *   FacebookSession.
     *
     * @return null|FacebookSession
     */
    public function getSessionFromRedirect()
    {
        $this->loadState();
        if ($this->isValidRedirect()) {
            $params = [
                'client_id' => FacebookSession::_getTargetAppId($this->appId),
                'redirect_uri' => $this->redirectUrl,
                'client_secret' => FacebookSession::_getTargetAppSecret($this->appSecret),
                'code' => $this->getCode(),
            ];
            $response = (new FacebookRequest(
                FacebookSession::newAppSession($this->appId, $this->appSecret),
                'GET',
                '/oauth/access_token',
                $params
            ))->execute()->getResponse();

            // print_r($response);
            if (isset($response->access_token)) {
                return new FacebookSession($response->access_token);
            }
        }

        return null;
    }

    /**
     * Generate a cryptographically secure pseudrandom number.
     *
     * @param int $bytes - number of bytes to return
     *
     * @return string
     *
     * @throws FacebookSDKException
     *
     * @todo Support Windows platforms
     */
    public function random($bytes)
    {
        if (!is_numeric($bytes)) {
            throw new FacebookSDKException(
                'random() expects an integer'
            );
        }
        if ($bytes < 1) {
            throw new FacebookSDKException(
                'random() expects an integer greater than zero'
            );
        }
        $buf = '';
        // http://sockpuppet.org/blog/2014/02/25/safely-generate-random-numbers/
        if (!\ini_get('open_basedir')
          && is_readable('/dev/urandom')) {
            $fp = fopen('/dev/urandom', 'r');
            if (false !== $fp) {
                $buf = fread($fp, $bytes);
                fclose($fp);
                if (false !== $buf) {
                    return bin2hex($buf);
                }
            }
        }

        if (\function_exists('mcrypt_create_iv')) {
            $buf = mcrypt_create_iv($bytes, MCRYPT_DEV_URANDOM);
            if (false !== $buf) {
                return bin2hex($buf);
            }
        }

        while (\strlen($buf) < $bytes) {
            $buf .= md5(uniqid(mt_rand(), true), true);
            // We are appending raw binary
        }

        return bin2hex(substr($buf, 0, $bytes));
    }

    /**
     * Disables the session_status() check when using $_SESSION.
     */
    public function disableSessionStatusCheck(): void
    {
        $this->checkForSessionStatus = false;
    }

    /**
     * Check if a redirect has a valid state.
     *
     * @return bool
     */
    protected function isValidRedirect()
    {
        return $this->getCode() && isset($_GET['state'])
            && $_GET['state'] === $this->state;
    }

    /**
     * Return the code.
     *
     * @return null|string
     */
    protected function getCode()
    {
        return $_GET['code'] ?? null;
    }

    /**
     * Stores a state string in session storage for CSRF protection.
     * Developers should subclass and override this method if they want to store
     *   this state in a different location.
     *
     * @param string $state
     *
     * @throws FacebookSDKException
     */
    protected function storeState($state): void
    {
        if (true === $this->checkForSessionStatus
          && PHP_SESSION_ACTIVE !== session_status()) {
            throw new FacebookSDKException(
                'Session not active, could not store state.',
                720
            );
        }
        $_SESSION[$this->sessionPrefix.'state'] = $state;
    }

    /**
     * Loads a state string from session storage for CSRF validation.  May return
     *   null if no object exists.  Developers should subclass and override this
     *   method if they want to load the state from a different location.
     *
     * @return null|string
     *
     * @throws FacebookSDKException
     */
    protected function loadState()
    {
        if (true === $this->checkForSessionStatus
          && PHP_SESSION_ACTIVE !== session_status()) {
            throw new FacebookSDKException(
                'Session not active, could not load state.',
                721
            );
        }
        if (isset($_SESSION[$this->sessionPrefix.'state'])) {
            $this->state = $_SESSION[$this->sessionPrefix.'state'];

            return $this->state;
        }

        return null;
    }
}
