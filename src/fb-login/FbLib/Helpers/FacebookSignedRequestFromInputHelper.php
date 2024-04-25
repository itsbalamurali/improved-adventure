<?php





namespace Facebook\Helpers;

use Facebook\Authentication\AccessToken;
use Facebook\Authentication\OAuth2Client;
use Facebook\Exceptions\FacebookSDKException;
use Facebook\Facebook;
use Facebook\FacebookApp;
use Facebook\FacebookClient;
use Facebook\SignedRequest;

/**
 * Class FacebookSignedRequestFromInputHelper.
 */
abstract class FacebookSignedRequestFromInputHelper
{
    /**
     * @var null|SignedRequest the SignedRequest entity
     */
    protected $signedRequest;

    /**
     * @var FacebookApp the FacebookApp entity
     */
    protected $app;

    /**
     * @var OAuth2Client The OAuth 2.0 client service.
     */
    protected $oAuth2Client;

    /**
     * Initialize the helper and process available signed request data.
     *
     * @param FacebookApp    $app          the FacebookApp entity
     * @param FacebookClient $client       the client to make HTTP requests
     * @param null|string    $graphVersion the version of Graph to use
     */
    public function __construct(FacebookApp $app, FacebookClient $client, $graphVersion = null)
    {
        $this->app = $app;
        $graphVersion = $graphVersion ?: Facebook::DEFAULT_GRAPH_VERSION;
        $this->oAuth2Client = new OAuth2Client($this->app, $client, $graphVersion);

        $this->instantiateSignedRequest();
    }

    /**
     * Instantiates a new SignedRequest entity.
     *
     * @param null|string
     * @param null|mixed $rawSignedRequest
     */
    public function instantiateSignedRequest($rawSignedRequest = null): void
    {
        $rawSignedRequest = $rawSignedRequest ?: $this->getRawSignedRequest();

        if (!$rawSignedRequest) {
            return;
        }

        $this->signedRequest = new SignedRequest($this->app, $rawSignedRequest);
    }

    /**
     * Returns an AccessToken entity from the signed request.
     *
     * @return null|AccessToken
     *
     * @throws FacebookSDKException
     */
    public function getAccessToken()
    {
        if ($this->signedRequest && $this->signedRequest->hasOAuthData()) {
            $code = $this->signedRequest->get('code');
            $accessToken = $this->signedRequest->get('oauth_token');

            if ($code && !$accessToken) {
                return $this->oAuth2Client->getAccessTokenFromCode($code);
            }

            $expiresAt = $this->signedRequest->get('expires', 0);

            return new AccessToken($accessToken, $expiresAt);
        }

        return null;
    }

    /**
     * Returns the SignedRequest entity.
     *
     * @return null|SignedRequest
     */
    public function getSignedRequest()
    {
        return $this->signedRequest;
    }

    /**
     * Returns the user_id if available.
     *
     * @return null|string
     */
    public function getUserId()
    {
        return $this->signedRequest ? $this->signedRequest->getUserId() : null;
    }

    /**
     * Get raw signed request from input.
     *
     * @return null|string
     */
    abstract public function getRawSignedRequest();

    /**
     * Get raw signed request from POST input.
     *
     * @return null|string
     */
    public function getRawSignedRequestFromPost()
    {
        if (isset($_POST['signed_request'])) {
            return $_POST['signed_request'];
        }

        return null;
    }

    /**
     * Get raw signed request from cookie set from the Javascript SDK.
     *
     * @return null|string
     */
    public function getRawSignedRequestFromCookie()
    {
        if (isset($_COOKIE['fbsr_'.$this->app->getId()])) {
            return $_COOKIE['fbsr_'.$this->app->getId()];
        }

        return null;
    }
}
