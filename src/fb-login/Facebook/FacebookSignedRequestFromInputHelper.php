<?php





namespace Facebook;

use Facebook\Entities\SignedRequest;

/**
 * Class FacebookSignedRequestFromInputHelper.
 */
abstract class FacebookSignedRequestFromInputHelper
{
    /**
     * @var null|string random string to prevent CSRF
     */
    public $state;

    /**
     * @var null|SignedRequest
     */
    protected $signedRequest;

    /**
     * @var string the app id
     */
    protected $appId;

    /**
     * @var string the app secret
     */
    protected $appSecret;

    /**
     * Initialize the helper and process available signed request data.
     *
     * @param null|string $appId
     * @param null|string $appSecret
     */
    public function __construct($appId = null, $appSecret = null)
    {
        $this->appId = FacebookSession::_getTargetAppId($appId);
        $this->appSecret = FacebookSession::_getTargetAppSecret($appSecret);

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

        $this->signedRequest = new SignedRequest($rawSignedRequest, $this->state, $this->appSecret);
    }

    /**
     * Instantiates a FacebookSession from the signed request from input.
     *
     * @return null|FacebookSession
     */
    public function getSession()
    {
        if ($this->signedRequest && $this->signedRequest->hasOAuthData()) {
            return FacebookSession::newSessionFromSignedRequest($this->signedRequest);
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
     * Get raw signed request from GET input.
     *
     * @return null|string
     */
    public function getRawSignedRequestFromGet()
    {
        if (isset($_GET['signed_request'])) {
            return $_GET['signed_request'];
        }

        return null;
    }

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
        if (isset($_COOKIE['fbsr_'.$this->appId])) {
            return $_COOKIE['fbsr_'.$this->appId];
        }

        return null;
    }
}
