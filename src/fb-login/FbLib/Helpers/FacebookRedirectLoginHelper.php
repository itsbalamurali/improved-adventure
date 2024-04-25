<?php





namespace Facebook\Helpers;

use Facebook\Authentication\AccessToken;
use Facebook\Authentication\OAuth2Client;
use Facebook\Exceptions\FacebookSDKException;
use Facebook\PersistentData\FacebookSessionPersistentDataHandler;
use Facebook\PersistentData\PersistentDataInterface;
use Facebook\PseudoRandomString\PseudoRandomStringGeneratorFactory;
use Facebook\PseudoRandomString\PseudoRandomStringGeneratorInterface;
use Facebook\Url\FacebookUrlDetectionHandler;
use Facebook\Url\FacebookUrlManipulator;
use Facebook\Url\UrlDetectionInterface;

/**
 * Class FacebookRedirectLoginHelper.
 */
class FacebookRedirectLoginHelper
{
    /**
     * @const int The length of CSRF string to validate the login link.
     */
    public const CSRF_LENGTH = 32;

    /**
     * @var OAuth2Client The OAuth 2.0 client service.
     */
    protected $oAuth2Client;

    /**
     * @var UrlDetectionInterface the URL detection handler
     */
    protected $urlDetectionHandler;

    /**
     * @var PersistentDataInterface the persistent data handler
     */
    protected $persistentDataHandler;

    /**
     * @var PseudoRandomStringGeneratorInterface the cryptographically secure pseudo-random string generator
     */
    protected $pseudoRandomStringGenerator;

    /**
     * @param OAuth2Client                              $oAuth2Client          The OAuth 2.0 client service.
     * @param null|PersistentDataInterface              $persistentDataHandler the persistent data handler
     * @param null|UrlDetectionInterface                $urlHandler            the URL detection handler
     * @param null|PseudoRandomStringGeneratorInterface $prsg                  the cryptographically secure pseudo-random string generator
     */
    public function __construct(OAuth2Client $oAuth2Client, ?PersistentDataInterface $persistentDataHandler = null, ?UrlDetectionInterface $urlHandler = null, ?PseudoRandomStringGeneratorInterface $prsg = null)
    {
        $this->oAuth2Client = $oAuth2Client;
        $this->persistentDataHandler = $persistentDataHandler ?: new FacebookSessionPersistentDataHandler();
        $this->urlDetectionHandler = $urlHandler ?: new FacebookUrlDetectionHandler();
        $this->pseudoRandomStringGenerator = PseudoRandomStringGeneratorFactory::createPseudoRandomStringGenerator($prsg);
    }

    /**
     * Returns the persistent data handler.
     *
     * @return PersistentDataInterface
     */
    public function getPersistentDataHandler()
    {
        return $this->persistentDataHandler;
    }

    /**
     * Returns the URL detection handler.
     *
     * @return UrlDetectionInterface
     */
    public function getUrlDetectionHandler()
    {
        return $this->urlDetectionHandler;
    }

    /**
     * Returns the cryptographically secure pseudo-random string generator.
     *
     * @return PseudoRandomStringGeneratorInterface
     */
    public function getPseudoRandomStringGenerator()
    {
        return $this->pseudoRandomStringGenerator;
    }

    /**
     * Returns the URL to send the user in order to login to Facebook.
     *
     * @param string $redirectUrl the URL Facebook should redirect users to after login
     * @param array  $scope       list of permissions to request during login
     * @param string $separator   the separator to use in http_build_query()
     *
     * @return string
     */
    public function getLoginUrl($redirectUrl, array $scope = [], $separator = '&')
    {
        return $this->makeUrl($redirectUrl, $scope, [], $separator);
    }

    /**
     * Returns the URL to send the user in order to log out of Facebook.
     *
     * @param AccessToken|string $accessToken the access token that will be logged out
     * @param string             $next        the url Facebook should redirect the user to after a successful logout
     * @param string             $separator   the separator to use in http_build_query()
     *
     * @return string
     *
     * @throws FacebookSDKException
     */
    public function getLogoutUrl($accessToken, $next, $separator = '&')
    {
        if (!$accessToken instanceof AccessToken) {
            $accessToken = new AccessToken($accessToken);
        }

        if ($accessToken->isAppAccessToken()) {
            throw new FacebookSDKException('Cannot generate a logout URL with an app access token.', 722);
        }

        $params = [
            'next' => $next,
            'access_token' => $accessToken->getValue(),
        ];

        return 'https://www.facebook.com/logout.php?'.http_build_query($params, null, $separator);
    }

    /**
     * Returns the URL to send the user in order to login to Facebook with permission(s) to be re-asked.
     *
     * @param string $redirectUrl the URL Facebook should redirect users to after login
     * @param array  $scope       list of permissions to request during login
     * @param string $separator   the separator to use in http_build_query()
     *
     * @return string
     */
    public function getReRequestUrl($redirectUrl, array $scope = [], $separator = '&')
    {
        $params = ['auth_type' => 'rerequest'];

        return $this->makeUrl($redirectUrl, $scope, $params, $separator);
    }

    /**
     * Returns the URL to send the user in order to login to Facebook with user to be re-authenticated.
     *
     * @param string $redirectUrl the URL Facebook should redirect users to after login
     * @param array  $scope       list of permissions to request during login
     * @param string $separator   the separator to use in http_build_query()
     *
     * @return string
     */
    public function getReAuthenticationUrl($redirectUrl, array $scope = [], $separator = '&')
    {
        $params = ['auth_type' => 'reauthenticate'];

        return $this->makeUrl($redirectUrl, $scope, $params, $separator);
    }

    /**
     * Takes a valid code from a login redirect, and returns an AccessToken entity.
     *
     * @param null|string $redirectUrl the redirect URL
     *
     * @return null|AccessToken
     *
     * @throws FacebookSDKException
     */
    public function getAccessToken($redirectUrl = null)
    {
        if (!$code = $this->getCode()) {
            return null;
        }

        $this->validateCsrf();
        $this->resetCsrf();

        $redirectUrl = $redirectUrl ?: $this->urlDetectionHandler->getCurrentUrl();
        // At minimum we need to remove the 'state' and 'code' params
        $redirectUrl = FacebookUrlManipulator::removeParamsFromUrl($redirectUrl, ['code', 'state']);

        return $this->oAuth2Client->getAccessTokenFromCode($code, $redirectUrl);
    }

    /**
     * Return the error code.
     *
     * @return null|string
     */
    public function getErrorCode()
    {
        return $this->getInput('error_code');
    }

    /**
     * Returns the error.
     *
     * @return null|string
     */
    public function getError()
    {
        return $this->getInput('error');
    }

    /**
     * Returns the error reason.
     *
     * @return null|string
     */
    public function getErrorReason()
    {
        return $this->getInput('error_reason');
    }

    /**
     * Returns the error description.
     *
     * @return null|string
     */
    public function getErrorDescription()
    {
        return $this->getInput('error_description');
    }

    /**
     * Validate the request against a cross-site request forgery.
     *
     * @throws FacebookSDKException
     */
    protected function validateCsrf(): void
    {
        $state = $this->getState();
        if (!$state) {
            throw new FacebookSDKException('Cross-site request forgery validation failed. Required GET param "state" missing.');
        }
        $savedState = $this->persistentDataHandler->get('state');
        if (!$savedState) {
            throw new FacebookSDKException('Cross-site request forgery validation failed. Required param "state" missing from persistent data.');
        }

        if (hash_equals($savedState, $state)) {
            return;
        }

        throw new FacebookSDKException('Cross-site request forgery validation failed. The "state" param from the URL and session do not match.');
    }

    /**
     * Return the code.
     *
     * @return null|string
     */
    protected function getCode()
    {
        return $this->getInput('code');
    }

    /**
     * Return the state.
     *
     * @return null|string
     */
    protected function getState()
    {
        return $this->getInput('state');
    }

    /**
     * Stores CSRF state and returns a URL to which the user should be sent to in order to continue the login process with Facebook.
     *
     * @param string $redirectUrl the URL Facebook should redirect users to after login
     * @param array  $scope       list of permissions to request during login
     * @param array  $params      an array of parameters to generate URL
     * @param string $separator   the separator to use in http_build_query()
     *
     * @return string
     */
    private function makeUrl($redirectUrl, array $scope, array $params = [], $separator = '&')
    {
        $state = $this->persistentDataHandler->get('state') ?: $this->pseudoRandomStringGenerator->getPseudoRandomString(static::CSRF_LENGTH);
        $this->persistentDataHandler->set('state', $state);

        return $this->oAuth2Client->getAuthorizationUrl($redirectUrl, $state, $scope, $params, $separator);
    }

    /**
     * Resets the CSRF so that it doesn't get reused.
     */
    private function resetCsrf(): void
    {
        $this->persistentDataHandler->set('state', null);
    }

    /**
     * Returns a value from a GET param.
     *
     * @param string $key
     *
     * @return null|string
     */
    private function getInput($key)
    {
        return $_GET[$key] ?? null;
    }
}
