<?php





namespace Facebook;

use Facebook\Exceptions\FacebookSDKException;
use Facebook\HttpClients\FacebookCurlHttpClient;
use Facebook\HttpClients\FacebookHttpClientInterface;
use Facebook\HttpClients\FacebookStreamHttpClient;

/**
 * Class FacebookClient.
 */
class FacebookClient
{
    /**
     * @const string Production Graph API URL.
     */
    public const BASE_GRAPH_URL = 'https://graph.facebook.com';

    /**
     * @const string Graph API URL for video uploads.
     */
    public const BASE_GRAPH_VIDEO_URL = 'https://graph-video.facebook.com';

    /**
     * @const string Beta Graph API URL.
     */
    public const BASE_GRAPH_URL_BETA = 'https://graph.beta.facebook.com';

    /**
     * @const string Beta Graph API URL for video uploads.
     */
    public const BASE_GRAPH_VIDEO_URL_BETA = 'https://graph-video.beta.facebook.com';

    /**
     * @const int The timeout in seconds for a normal request.
     */
    public const DEFAULT_REQUEST_TIMEOUT = 60;

    /**
     * @const int The timeout in seconds for a request that contains file uploads.
     */
    public const DEFAULT_FILE_UPLOAD_REQUEST_TIMEOUT = 3_600;

    /**
     * @const int The timeout in seconds for a request that contains video uploads.
     */
    public const DEFAULT_VIDEO_UPLOAD_REQUEST_TIMEOUT = 7_200;

    /**
     * @var int the number of calls that have been made to Graph
     */
    public static $requestCount = 0;

    /**
     * @var bool toggle to use Graph beta url
     */
    protected $enableBetaMode = false;

    /**
     * @var FacebookHttpClientInterface HTTP client handler
     */
    protected $httpClientHandler;

    /**
     * Instantiates a new FacebookClient object.
     *
     * @param bool $enableBeta
     */
    public function __construct(?FacebookHttpClientInterface $httpClientHandler = null, $enableBeta = false)
    {
        $this->httpClientHandler = $httpClientHandler ?: $this->detectHttpClientHandler();
        $this->enableBetaMode = $enableBeta;
    }

    /**
     * Sets the HTTP client handler.
     */
    public function setHttpClientHandler(FacebookHttpClientInterface $httpClientHandler): void
    {
        $this->httpClientHandler = $httpClientHandler;
    }

    /**
     * Returns the HTTP client handler.
     *
     * @return FacebookHttpClientInterface
     */
    public function getHttpClientHandler()
    {
        return $this->httpClientHandler;
    }

    /**
     * Detects which HTTP client handler to use.
     *
     * @return FacebookHttpClientInterface
     */
    public function detectHttpClientHandler()
    {
        return \extension_loaded('curl') ? new FacebookCurlHttpClient() : new FacebookStreamHttpClient();
    }

    /**
     * Toggle beta mode.
     *
     * @param bool $betaMode
     */
    public function enableBetaMode($betaMode = true): void
    {
        $this->enableBetaMode = $betaMode;
    }

    /**
     * Returns the base Graph URL.
     *
     * @param bool $postToVideoUrl post to the video API if videos are being uploaded
     *
     * @return string
     */
    public function getBaseGraphUrl($postToVideoUrl = false)
    {
        if ($postToVideoUrl) {
            return $this->enableBetaMode ? static::BASE_GRAPH_VIDEO_URL_BETA : static::BASE_GRAPH_VIDEO_URL;
        }

        return $this->enableBetaMode ? static::BASE_GRAPH_URL_BETA : static::BASE_GRAPH_URL;
    }

    /**
     * Prepares the request for sending to the client handler.
     *
     * @return array
     */
    public function prepareRequestMessage(FacebookRequest $request)
    {
        $postToVideoUrl = $request->containsVideoUploads();
        $url = $this->getBaseGraphUrl($postToVideoUrl).$request->getUrl();

        // If we're sending files they should be sent as multipart/form-data
        if ($request->containsFileUploads()) {
            $requestBody = $request->getMultipartBody();
            $request->setHeaders([
                'Content-Type' => 'multipart/form-data; boundary='.$requestBody->getBoundary(),
            ]);
        } else {
            $requestBody = $request->getUrlEncodedBody();
            $request->setHeaders([
                'Content-Type' => 'application/x-www-form-urlencoded',
            ]);
        }

        return [
            $url,
            $request->getMethod(),
            $request->getHeaders(),
            $requestBody->getBody(),
        ];
    }

    /**
     * Makes the request to Graph and returns the result.
     *
     * @return FacebookResponse
     *
     * @throws FacebookSDKException
     */
    public function sendRequest(FacebookRequest $request)
    {
        if ('Facebook\FacebookRequest' === \get_class($request)) {
            $request->validateAccessToken();
        }

        [$url, $method, $headers, $body] = $this->prepareRequestMessage($request);

        // Since file uploads can take a while, we need to give more time for uploads
        $timeOut = static::DEFAULT_REQUEST_TIMEOUT;
        if ($request->containsFileUploads()) {
            $timeOut = static::DEFAULT_FILE_UPLOAD_REQUEST_TIMEOUT;
        } elseif ($request->containsVideoUploads()) {
            $timeOut = static::DEFAULT_VIDEO_UPLOAD_REQUEST_TIMEOUT;
        }

        // Should throw `FacebookSDKException` exception on HTTP client error.
        // Don't catch to allow it to bubble up.
        $rawResponse = $this->httpClientHandler->send($url, $method, $body, $headers, $timeOut);

        ++static::$requestCount;

        $returnResponse = new FacebookResponse(
            $request,
            $rawResponse->getBody(),
            $rawResponse->getHttpResponseCode(),
            $rawResponse->getHeaders()
        );

        if ($returnResponse->isError()) {
            throw $returnResponse->getThrownException();
        }

        return $returnResponse;
    }

    /**
     * Makes a batched request to Graph and returns the result.
     *
     * @return FacebookBatchResponse
     *
     * @throws FacebookSDKException
     */
    public function sendBatchRequest(FacebookBatchRequest $request)
    {
        $request->prepareRequestsForBatch();
        $facebookResponse = $this->sendRequest($request);

        return new FacebookBatchResponse($request, $facebookResponse);
    }
}
