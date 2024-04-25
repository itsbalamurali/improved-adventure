<?php





namespace Facebook;

use Facebook\HttpClients\FacebookCurlHttpClient;
use Facebook\HttpClients\FacebookHttpable;
use Facebook\HttpClients\FacebookStreamHttpClient;

/**
 * Class FacebookRequest.
 *
 * @author Fosco Marotto <fjm@fb.com>
 * @author David Poll <depoll@fb.com>
 */
class FacebookRequest
{
    /**
     * @const string Version number of the Facebook PHP SDK.
     */
    public const VERSION = '4.0.15';

    /**
     * @const string Default Graph API version for requests
     */
    public const GRAPH_API_VERSION = 'v2.2';

    /**
     * @const string Graph API URL
     */
    public const BASE_GRAPH_URL = 'https://graph.facebook.com';

    /**
     * @var int the number of calls that have been made to Graph
     */
    public static $requestCount = 0;

    /**
     * @var FacebookSession The session used for this request
     */
    private $session;

    /**
     * @var string The HTTP method for the request
     */
    private $method;

    /**
     * @var string The path for the request
     */
    private $path;

    /**
     * @var array The parameters for the request
     */
    private $params;

    /**
     * @var string The Graph API version for the request
     */
    private $version;

    /**
     * @var string ETag sent with the request
     */
    private $etag;

    /**
     * @var FacebookHttpable HTTP client handler
     */
    private static $httpClientHandler;

    /**
     * FacebookRequest - Returns a new request using the given session.  optional
     *   parameters hash will be sent with the request.  This object is
     *   immutable.
     *
     * @param string      $method
     * @param string      $path
     * @param null|array  $parameters
     * @param null|string $version
     * @param null|string $etag
     */
    public function __construct(
        FacebookSession $session,
        $method,
        $path,
        $parameters = null,
        $version = null,
        $etag = null
    ) {
        $this->session = $session;
        $this->method = $method;
        $this->path = $path;
        if ($version) {
            $this->version = $version;
        } else {
            $this->version = static::GRAPH_API_VERSION;
        }
        $this->etag = $etag;

        $params = ($parameters ?: []);
        if ($session
          && !isset($params['access_token'])) {
            $params['access_token'] = $session->getToken();
        }
        if (FacebookSession::useAppSecretProof()
          && !isset($params['appsecret_proof'])) {
            $params['appsecret_proof'] = $this->getAppSecretProof(
                $params['access_token']
            );
        }
        $this->params = $params;
    }

    /**
     * getSession - Returns the associated FacebookSession.
     *
     * @return FacebookSession
     */
    public function getSession()
    {
        return $this->session;
    }

    /**
     * getPath - Returns the associated path.
     *
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * getParameters - Returns the associated parameters.
     *
     * @return array
     */
    public function getParameters()
    {
        return $this->params;
    }

    /**
     * getMethod - Returns the associated method.
     *
     * @return string
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * getETag - Returns the ETag sent with the request.
     *
     * @return string
     */
    public function getETag()
    {
        return $this->etag;
    }

    /**
     * setHttpClientHandler - Returns an instance of the HTTP client
     * handler.
     *
     * @param FacebookHttpable
     */
    public static function setHttpClientHandler(FacebookHttpable $handler): void
    {
        static::$httpClientHandler = $handler;
    }

    /**
     * getHttpClientHandler - Returns an instance of the HTTP client
     * data handler.
     *
     * @return FacebookHttpable
     */
    public static function getHttpClientHandler()
    {
        if (static::$httpClientHandler) {
            return static::$httpClientHandler;
        }

        return \function_exists('curl_init') ? new FacebookCurlHttpClient() : new FacebookStreamHttpClient();
    }

    /**
     * execute - Makes the request to Facebook and returns the result.
     *
     * @return FacebookResponse
     *
     * @throws FacebookSDKException
     * @throws FacebookRequestException
     */
    public function execute()
    {
        $url = $this->getRequestURL();
        $params = $this->getParameters();

        if ('GET' === $this->method) {
            $url = self::appendParamsToUrl($url, $params);
            $params = [];
        }

        $connection = self::getHttpClientHandler();
        $connection->addRequestHeader('User-Agent', 'fb-php-'.self::VERSION);
        $connection->addRequestHeader('Accept-Encoding', '*'); // Support all available encodings.

        // ETag
        if (isset($this->etag)) {
            $connection->addRequestHeader('If-None-Match', $this->etag);
        }

        // Should throw `FacebookSDKException` exception on HTTP client error.
        // Don't catch to allow it to bubble up.
        $result = $connection->send($url, $this->method, $params);

        ++static::$requestCount;

        $etagHit = 304 === $connection->getResponseHttpStatusCode();

        $headers = $connection->getResponseHeaders();
        $etagReceived = $headers['ETag'] ?? null;

        $decodedResult = json_decode($result);
        if (null === $decodedResult) {
            $out = [];
            parse_str($result, $out);

            return new FacebookResponse($this, $out, $result, $etagHit, $etagReceived);
        }
        if (isset($decodedResult->error)) {
            throw FacebookRequestException::create(
                $result,
                $decodedResult->error,
                $connection->getResponseHttpStatusCode()
            );
        }

        return new FacebookResponse($this, $decodedResult, $result, $etagHit, $etagReceived);
    }

    /**
     * Generate and return the appsecret_proof value for an access_token.
     *
     * @param string $token
     *
     * @return string
     */
    public function getAppSecretProof($token)
    {
        return hash_hmac('sha256', $token, FacebookSession::_getTargetAppSecret());
    }

    /**
     * appendParamsToUrl - Gracefully appends params to the URL.
     *
     * @param string $url
     * @param array  $params
     *
     * @return string
     */
    public static function appendParamsToUrl($url, $params = [])
    {
        if (!$params) {
            return $url;
        }

        if (!str_contains($url, '?')) {
            return $url.'?'.http_build_query($params, null, '&');
        }

        [$path, $query_string] = explode('?', $url, 2);
        parse_str($query_string, $query_array);

        // Favor params from the original URL over $params
        $params = array_merge($params, $query_array);

        return $path.'?'.http_build_query($params, null, '&');
    }

    /**
     * Returns the base Graph URL.
     *
     * @return string
     */
    protected function getRequestURL()
    {
        return static::BASE_GRAPH_URL.'/'.$this->version.$this->path;
    }
}
