<?php





namespace Facebook\Authentication;

use Facebook\Exceptions\FacebookResponseException;
use Facebook\Exceptions\FacebookSDKException;
use Facebook\Facebook;
use Facebook\FacebookApp;
use Facebook\FacebookClient;
use Facebook\FacebookRequest;
use Facebook\FacebookResponse;

/**
 * Class OAuth2Client.
 */
class OAuth2Client
{
    /**
     * @const string The base authorization URL.
     */
    public const BASE_AUTHORIZATION_URL = 'https://www.facebook.com';

    /**
     * The FacebookApp entity.
     *
     * @var FacebookApp
     */
    protected $app;

    /**
     * The Facebook client.
     *
     * @var FacebookClient
     */
    protected $client;

    /**
     * The version of the Graph API to use.
     *
     * @var string
     */
    protected $graphVersion;

    /**
     * The last request sent to Graph.
     *
     * @var null|FacebookRequest
     */
    protected $lastRequest;

    /**
     * @param null|string $graphVersion the version of the Graph API to use
     */
    public function __construct(FacebookApp $app, FacebookClient $client, $graphVersion = null)
    {
        $this->app = $app;
        $this->client = $client;
        $this->graphVersion = $graphVersion ?: Facebook::DEFAULT_GRAPH_VERSION;
    }

    /**
     * Returns the last FacebookRequest that was sent.
     * Useful for debugging and testing.
     *
     * @return null|FacebookRequest
     */
    public function getLastRequest()
    {
        return $this->lastRequest;
    }

    /**
     * Get the metadata associated with the access token.
     *
     * @param AccessToken|string $accessToken the access token to debug
     *
     * @return AccessTokenMetadata
     */
    public function debugToken($accessToken)
    {
        $accessToken = $accessToken instanceof AccessToken ? $accessToken->getValue() : $accessToken;
        $params = ['input_token' => $accessToken];

        $this->lastRequest = new FacebookRequest(
            $this->app,
            $this->app->getAccessToken(),
            'GET',
            '/debug_token',
            $params,
            null,
            $this->graphVersion
        );
        $response = $this->client->sendRequest($this->lastRequest);
        $metadata = $response->getDecodedBody();

        return new AccessTokenMetadata($metadata);
    }

    /**
     * Generates an authorization URL to begin the process of authenticating a user.
     *
     * @param string $redirectUrl the callback URL to redirect to
     * @param string $state       the CSPRNG-generated CSRF value
     * @param array  $scope       an array of permissions to request
     * @param array  $params      an array of parameters to generate URL
     * @param string $separator   the separator to use in http_build_query()
     *
     * @return string
     */
    public function getAuthorizationUrl($redirectUrl, $state, array $scope = [], array $params = [], $separator = '&')
    {
        $params += [
            'client_id' => $this->app->getId(),
            'state' => $state,
            'response_type' => 'code',
            'sdk' => 'php-sdk-'.Facebook::VERSION,
            'redirect_uri' => $redirectUrl,
            'scope' => implode(',', $scope),
        ];

        return static::BASE_AUTHORIZATION_URL.'/'.$this->graphVersion.'/dialog/oauth?'.http_build_query($params, null, $separator);
    }

    /**
     * Get a valid access token from a code.
     *
     * @param string $code
     * @param string $redirectUri
     *
     * @return AccessToken
     *
     * @throws FacebookSDKException
     */
    public function getAccessTokenFromCode($code, $redirectUri = '')
    {
        $params = [
            'code' => $code,
            'redirect_uri' => $redirectUri,
        ];

        return $this->requestAnAccessToken($params);
    }

    /**
     * Exchanges a short-lived access token with a long-lived access token.
     *
     * @param AccessToken|string $accessToken
     *
     * @return AccessToken
     *
     * @throws FacebookSDKException
     */
    public function getLongLivedAccessToken($accessToken)
    {
        $accessToken = $accessToken instanceof AccessToken ? $accessToken->getValue() : $accessToken;
        $params = [
            'grant_type' => 'fb_exchange_token',
            'fb_exchange_token' => $accessToken,
        ];

        return $this->requestAnAccessToken($params);
    }

    /**
     * Get a valid code from an access token.
     *
     * @param AccessToken|string $accessToken
     * @param string             $redirectUri
     *
     * @return AccessToken
     *
     * @throws FacebookSDKException
     */
    public function getCodeFromLongLivedAccessToken($accessToken, $redirectUri = '')
    {
        $params = [
            'redirect_uri' => $redirectUri,
        ];

        $response = $this->sendRequestWithClientParams('/oauth/client_code', $params, $accessToken);
        $data = $response->getDecodedBody();

        if (!isset($data['code'])) {
            throw new FacebookSDKException('Code was not returned from Graph.', 401);
        }

        return $data['code'];
    }

    /**
     * Send a request to the OAuth endpoint.
     *
     * @return AccessToken
     *
     * @throws FacebookSDKException
     */
    protected function requestAnAccessToken(array $params)
    {
        $response = $this->sendRequestWithClientParams('/oauth/access_token', $params);
        $data = $response->getDecodedBody();

        if (!isset($data['access_token'])) {
            throw new FacebookSDKException('Access token was not returned from Graph.', 401);
        }

        // Graph returns two different key names for expiration time
        // on the same endpoint. Doh! :/
        $expiresAt = 0;
        if (isset($data['expires'])) {
            // For exchanging a short lived token with a long lived token.
            // The expiration time in seconds will be returned as "expires".
            $expiresAt = time() + $data['expires'];
        } elseif (isset($data['expires_in'])) {
            // For exchanging a code for a short lived access token.
            // The expiration time in seconds will be returned as "expires_in".
            // See: https://developers.facebook.com/docs/facebook-login/access-tokens#long-via-code
            $expiresAt = time() + $data['expires_in'];
        }

        return new AccessToken($data['access_token'], $expiresAt);
    }

    /**
     * Send a request to Graph with an app access token.
     *
     * @param string                  $endpoint
     * @param null|AccessToken|string $accessToken
     *
     * @return FacebookResponse
     *
     * @throws FacebookResponseException
     */
    protected function sendRequestWithClientParams($endpoint, array $params, $accessToken = null)
    {
        $params += $this->getClientParams();

        $accessToken = $accessToken ?: $this->app->getAccessToken();

        $this->lastRequest = new FacebookRequest(
            $this->app,
            $accessToken,
            'GET',
            $endpoint,
            $params,
            null,
            $this->graphVersion
        );

        return $this->client->sendRequest($this->lastRequest);
    }

    /**
     * Returns the client_* params for OAuth requests.
     *
     * @return array
     */
    protected function getClientParams()
    {
        return [
            'client_id' => $this->app->getId(),
            'client_secret' => $this->app->getSecret(),
        ];
    }
}
