<?php





namespace Facebook\Entities;

use Facebook\FacebookRequest;
use Facebook\FacebookRequestException;
use Facebook\FacebookResponse;
use Facebook\FacebookSession;
use Facebook\GraphSessionInfo;

/**
 * Class AccessToken.
 */
class AccessToken
{
    /**
     * The access token.
     *
     * @var string
     */
    protected $accessToken;

    /**
     * A unique ID to identify a client.
     *
     * @var string
     */
    protected $machineId;

    /**
     * Date when token expires.
     *
     * @var null|\DateTime
     */
    protected $expiresAt;

    /**
     * Create a new access token entity.
     *
     * @param string $accessToken
     * @param int    $expiresAt
     * @param null|string machineId
     * @param null|mixed $machineId
     */
    public function __construct($accessToken, $expiresAt = 0, $machineId = null)
    {
        $this->accessToken = $accessToken;
        if ($expiresAt) {
            $this->setExpiresAtFromTimeStamp($expiresAt);
        }
        $this->machineId = $machineId;
    }

    /**
     * Returns the access token as a string.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->accessToken;
    }

    /**
     * Getter for expiresAt.
     *
     * @return null|\DateTime
     */
    public function getExpiresAt()
    {
        return $this->expiresAt;
    }

    /**
     * Getter for machineId.
     *
     * @return null|string
     */
    public function getMachineId()
    {
        return $this->machineId;
    }

    /**
     * Determines whether or not this is a long-lived token.
     *
     * @return bool
     */
    public function isLongLived()
    {
        if ($this->expiresAt) {
            return $this->expiresAt->getTimestamp() > time() + (60 * 60 * 2);
        }

        return false;
    }

    /**
     * Checks the validity of the access token.
     *
     * @param null|string $appId     Application ID to use
     * @param null|string $appSecret App secret value to use
     * @param null|string $machineId
     *
     * @return bool
     */
    public function isValid($appId = null, $appSecret = null, $machineId = null)
    {
        $accessTokenInfo = $this->getInfo($appId, $appSecret);
        $machineId = $machineId ?: $this->machineId;

        return static::validateAccessToken($accessTokenInfo, $appId, $machineId);
    }

    /**
     * Ensures the provided GraphSessionInfo object is valid,
     *   throwing an exception if not.  Ensures the appId matches,
     *   that the machineId matches if it's being used,
     *   that the token is valid and has not expired.
     *
     * @param null|string $appId     Application ID to use
     * @param null|string $machineId
     *
     * @return bool
     */
    public static function validateAccessToken(
        GraphSessionInfo $tokenInfo,
        $appId = null,
        $machineId = null
    ) {
        $targetAppId = FacebookSession::_getTargetAppId($appId);

        $appIdIsValid = $tokenInfo->getAppId() === $targetAppId;
        $machineIdIsValid = $tokenInfo->getProperty('machine_id') === $machineId;
        $accessTokenIsValid = $tokenInfo->isValid();

        // Not all access tokens return an expiration. E.g. an app access token.
        if ($tokenInfo->getExpiresAt() instanceof \DateTime) {
            $accessTokenIsStillAlive = $tokenInfo->getExpiresAt()->getTimestamp() >= time();
        } else {
            $accessTokenIsStillAlive = true;
        }

        return $appIdIsValid && $machineIdIsValid && $accessTokenIsValid && $accessTokenIsStillAlive;
    }

    /**
     * Get a valid access token from a code.
     *
     * @param string      $code
     * @param null|string $appId
     * @param null|string $appSecret
     * @param null|string $machineId
     *
     * @return AccessToken
     */
    public static function getAccessTokenFromCode($code, $appId = null, $appSecret = null, $machineId = null)
    {
        $params = [
            'code' => $code,
            'redirect_uri' => '',
        ];

        if ($machineId) {
            $params['machine_id'] = $machineId;
        }

        return static::requestAccessToken($params, $appId, $appSecret);
    }

    /**
     * Get a valid code from an access token.
     *
     * @param AccessToken|string $accessToken
     * @param null|string        $appId
     * @param null|string        $appSecret
     *
     * @return AccessToken
     */
    public static function getCodeFromAccessToken($accessToken, $appId = null, $appSecret = null)
    {
        $accessToken = (string) $accessToken;

        $params = [
            'access_token' => $accessToken,
            'redirect_uri' => '',
        ];

        return static::requestCode($params, $appId, $appSecret);
    }

    /**
     * Exchanges a short lived access token with a long lived access token.
     *
     * @param null|string $appId
     * @param null|string $appSecret
     *
     * @return AccessToken
     */
    public function extend($appId = null, $appSecret = null)
    {
        $params = [
            'grant_type' => 'fb_exchange_token',
            'fb_exchange_token' => $this->accessToken,
        ];

        return static::requestAccessToken($params, $appId, $appSecret);
    }

    /**
     * Request an access token based on a set of params.
     *
     * @param null|string $appId
     * @param null|string $appSecret
     *
     * @return AccessToken
     *
     * @throws FacebookRequestException
     */
    public static function requestAccessToken(array $params, $appId = null, $appSecret = null)
    {
        $response = static::request('/oauth/access_token', $params, $appId, $appSecret);
        $data = $response->getResponse();

        /*
         * @TODO fix this malarkey - getResponse() should always return an object
         * @see https://github.com/facebook/facebook-php-sdk-v4/issues/36
         */
        if (\is_array($data)) {
            if (isset($data['access_token'])) {
                $expiresAt = isset($data['expires']) ? time() + $data['expires'] : 0;

                return new static($data['access_token'], $expiresAt);
            }
        } elseif ($data instanceof \stdClass) {
            if (isset($data->access_token)) {
                $expiresAt = isset($data->expires_in) ? time() + $data->expires_in : 0;
                $machineId = isset($data->machine_id) ? (string) $data->machine_id : null;

                return new static((string) $data->access_token, $expiresAt, $machineId);
            }
        }

        throw FacebookRequestException::create(
            $response->getRawResponse(),
            $data,
            401
        );
    }

    /**
     * Request a code from a long lived access token.
     *
     * @param null|string $appId
     * @param null|string $appSecret
     *
     * @return string
     *
     * @throws FacebookRequestException
     */
    public static function requestCode(array $params, $appId = null, $appSecret = null)
    {
        $response = static::request('/oauth/client_code', $params, $appId, $appSecret);
        $data = $response->getResponse();

        if (isset($data->code)) {
            return (string) $data->code;
        }

        throw FacebookRequestException::create(
            $response->getRawResponse(),
            $data,
            401
        );
    }

    /**
     * Get more info about an access token.
     *
     * @param null|string $appId
     * @param null|string $appSecret
     *
     * @return GraphSessionInfo
     */
    public function getInfo($appId = null, $appSecret = null)
    {
        $params = ['input_token' => $this->accessToken];

        $request = new FacebookRequest(
            FacebookSession::newAppSession($appId, $appSecret),
            'GET',
            '/debug_token',
            $params
        );
        $response = $request->execute()->getGraphObject(GraphSessionInfo::className());

        // Update the data on this token
        if ($response->getExpiresAt()) {
            $this->expiresAt = $response->getExpiresAt();
        }

        return $response;
    }

    /**
     * Returns true if the access token is an app session token.
     *
     * @return bool
     */
    public function isAppSession()
    {
        return str_contains($this->accessToken, '|');
    }

    /**
     * Setter for expires_at.
     *
     * @param int $timeStamp
     */
    protected function setExpiresAtFromTimeStamp($timeStamp): void
    {
        $dt = new \DateTime();
        $dt->setTimestamp($timeStamp);
        $this->expiresAt = $dt;
    }

    /**
     * Send a request to Graph with an app access token.
     *
     * @param string      $endpoint
     * @param null|string $appId
     * @param null|string $appSecret
     *
     * @return FacebookResponse
     *
     * @throws FacebookRequestException
     */
    protected static function request($endpoint, array $params, $appId = null, $appSecret = null)
    {
        $targetAppId = FacebookSession::_getTargetAppId($appId);
        $targetAppSecret = FacebookSession::_getTargetAppSecret($appSecret);

        if (!isset($params['client_id'])) {
            $params['client_id'] = $targetAppId;
        }
        if (!isset($params['client_secret'])) {
            $params['client_secret'] = $targetAppSecret;
        }

        // The response for this endpoint is not JSON, so it must be handled
        //   differently, not as a GraphObject.
        $request = new FacebookRequest(
            FacebookSession::newAppSession($targetAppId, $targetAppSecret),
            'GET',
            $endpoint,
            $params
        );

        return $request->execute();
    }
}
