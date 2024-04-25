<?php





namespace Facebook\HttpClients;

use Facebook\FacebookSDKException;

/**
 * Class FacebookCurlHttpClient.
 */
class FacebookCurlHttpClient implements FacebookHttpable
{
    /**
     * @const Curl Version which is unaffected by the proxy header length error.
     */
    public const CURL_PROXY_QUIRK_VER = 0x07_1E_00;

    /**
     * @const "Connection Established" header text
     */
    public const CONNECTION_ESTABLISHED = "HTTP/1.0 200 Connection established\r\n\r\n";

    /**
     * @var array The headers to be sent with the request
     */
    protected $requestHeaders = [];

    /**
     * @var array The headers received from the response
     */
    protected $responseHeaders = [];

    /**
     * @var int The HTTP status code returned from the server
     */
    protected $responseHttpStatusCode = 0;

    /**
     * @var string The client error message
     */
    protected $curlErrorMessage = '';

    /**
     * @var int The curl client error code
     */
    protected $curlErrorCode = 0;

    /**
     * @var bool|string The raw response from the server
     */
    protected $rawResponse;

    /**
     * @var FacebookCurl Procedural curl as object
     */
    protected static $facebookCurl;

    /**
     * @var bool If IPv6 should be disabled
     */
    protected static $disableIPv6;

    /**
     * @param null|FacebookCurl Procedural curl as object
     */
    public function __construct(?FacebookCurl $facebookCurl = null)
    {
        self::$facebookCurl = $facebookCurl ?: new FacebookCurl();
        self::$disableIPv6 = self::$disableIPv6 ?: false;
    }

    /**
     * Disable IPv6 resolution.
     */
    public function disableIPv6(): void
    {
        self::$disableIPv6 = true;
    }

    /**
     * The headers we want to send with the request.
     *
     * @param string $key
     * @param string $value
     */
    public function addRequestHeader($key, $value): void
    {
        $this->requestHeaders[$key] = $value;
    }

    /**
     * The headers returned in the response.
     *
     * @return array
     */
    public function getResponseHeaders()
    {
        return $this->responseHeaders;
    }

    /**
     * The HTTP status response code.
     *
     * @return int
     */
    public function getResponseHttpStatusCode()
    {
        return $this->responseHttpStatusCode;
    }

    /**
     * Sends a request to the server.
     *
     * @param string $url        The endpoint to send the request to
     * @param string $method     The request method
     * @param array  $parameters The key value pairs to be sent in the body
     *
     * @return string Raw response from the server
     *
     * @throws FacebookSDKException
     */
    public function send($url, $method = 'GET', $parameters = [])
    {
        $this->openConnection($url, $method, $parameters);
        $this->tryToSendRequest();

        if ($this->curlErrorCode) {
            throw new FacebookSDKException($this->curlErrorMessage, $this->curlErrorCode);
        }

        // Separate the raw headers from the raw body
        [$rawHeaders, $rawBody] = $this->extractResponseHeadersAndBody();

        $this->responseHeaders = self::headersToArray($rawHeaders);

        $this->closeConnection();

        return $rawBody;
    }

    /**
     * Opens a new curl connection.
     *
     * @param string $url        The endpoint to send the request to
     * @param string $method     The request method
     * @param array  $parameters The key value pairs to be sent in the body
     */
    public function openConnection($url, $method = 'GET', $parameters = []): void
    {
        $options = [
            CURLOPT_URL => $url,
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_TIMEOUT => 60,
            CURLOPT_RETURNTRANSFER => true, // Follow 301 redirects
            CURLOPT_HEADER => true, // Enable header processing
            CURLOPT_SSL_VERIFYHOST => 2,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_CAINFO => __DIR__.'/certs/DigiCertHighAssuranceEVRootCA.pem',
        ];

        if ('GET' !== $method) {
            $options[CURLOPT_POSTFIELDS] = $parameters;
        }
        if ('DELETE' === $method || 'PUT' === $method) {
            $options[CURLOPT_CUSTOMREQUEST] = $method;
        }

        if (!empty($this->requestHeaders)) {
            $options[CURLOPT_HTTPHEADER] = $this->compileRequestHeaders();
        }

        if (self::$disableIPv6) {
            $options[CURLOPT_IPRESOLVE] = CURL_IPRESOLVE_V4;
        }

        self::$facebookCurl->init();
        self::$facebookCurl->setopt_array($options);
    }

    /**
     * Closes an existing curl connection.
     */
    public function closeConnection(): void
    {
        self::$facebookCurl->close();
    }

    /**
     * Try to send the request.
     */
    public function tryToSendRequest(): void
    {
        $this->sendRequest();
        $this->curlErrorMessage = self::$facebookCurl->error();
        $this->curlErrorCode = self::$facebookCurl->errno();
        $this->responseHttpStatusCode = self::$facebookCurl->getinfo(CURLINFO_HTTP_CODE);
    }

    /**
     * Send the request and get the raw response from curl.
     */
    public function sendRequest(): void
    {
        $this->rawResponse = self::$facebookCurl->exec();
    }

    /**
     * Compiles the request headers into a curl-friendly format.
     *
     * @return array
     */
    public function compileRequestHeaders()
    {
        $return = [];

        foreach ($this->requestHeaders as $key => $value) {
            $return[] = $key.': '.$value;
        }

        return $return;
    }

    /**
     * Extracts the headers and the body into a two-part array.
     *
     * @return array
     */
    public function extractResponseHeadersAndBody()
    {
        $headerSize = self::getHeaderSize();

        $rawHeaders = mb_substr($this->rawResponse, 0, $headerSize);
        $rawBody = mb_substr($this->rawResponse, $headerSize);

        return [trim($rawHeaders), trim($rawBody)];
    }

    /**
     * Converts raw header responses into an array.
     *
     * @param string $rawHeaders
     *
     * @return array
     */
    public static function headersToArray($rawHeaders)
    {
        $headers = [];

        // Normalize line breaks
        $rawHeaders = str_replace("\r\n", "\n", $rawHeaders);

        // There will be multiple headers if a 301 was followed
        // or a proxy was followed, etc
        $headerCollection = explode("\n\n", trim($rawHeaders));
        // We just want the last response (at the end)
        $rawHeader = array_pop($headerCollection);

        $headerComponents = explode("\n", $rawHeader);
        foreach ($headerComponents as $line) {
            if (!str_contains($line, ': ')) {
                $headers['http_code'] = $line;
            } else {
                [$key, $value] = explode(': ', $line);
                $headers[$key] = $value;
            }
        }

        return $headers;
    }

    /**
     * Return proper header size.
     *
     * @return int
     */
    private function getHeaderSize()
    {
        $headerSize = self::$facebookCurl->getinfo(CURLINFO_HEADER_SIZE);
        // This corrects a Curl bug where header size does not account
        // for additional Proxy headers.
        if (self::needsCurlProxyFix()) {
            // Additional way to calculate the request body size.
            if (preg_match('/Content-Length: (\d+)/', $this->rawResponse, $m)) {
                $headerSize = mb_strlen($this->rawResponse) - $m[1];
            } elseif (false !== stripos($this->rawResponse, self::CONNECTION_ESTABLISHED)) {
                $headerSize += mb_strlen(self::CONNECTION_ESTABLISHED);
            }
        }

        return $headerSize;
    }

    /**
     * Detect versions of Curl which report incorrect header lengths when
     * using Proxies.
     *
     * @return bool
     */
    private static function needsCurlProxyFix()
    {
        $ver = self::$facebookCurl->version();
        $version = $ver['version_number'];

        return $version < self::CURL_PROXY_QUIRK_VER;
    }
}
