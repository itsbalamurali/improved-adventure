<?php





namespace Facebook\HttpClients;

use Facebook\Exceptions\FacebookSDKException;
use Facebook\Http\GraphRawResponse;

/**
 * Class FacebookCurlHttpClient.
 */
class FacebookCurlHttpClient implements FacebookHttpClientInterface
{
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
    protected $facebookCurl;

    /**
     * @param null|FacebookCurl Procedural curl as object
     */
    public function __construct(?FacebookCurl $facebookCurl = null)
    {
        $this->facebookCurl = $facebookCurl ?: new FacebookCurl();
    }

    public function send($url, $method, $body, array $headers, $timeOut)
    {
        $this->openConnection($url, $method, $body, $headers, $timeOut);
        $this->sendRequest();

        if ($curlErrorCode = $this->facebookCurl->errno()) {
            throw new FacebookSDKException($this->facebookCurl->error(), $curlErrorCode);
        }

        // Separate the raw headers from the raw body
        [$rawHeaders, $rawBody] = $this->extractResponseHeadersAndBody();

        $this->closeConnection();

        return new GraphRawResponse($rawHeaders, $rawBody);
    }

    /**
     * Opens a new curl connection.
     *
     * @param string $url     the endpoint to send the request to
     * @param string $method  the request method
     * @param string $body    the body of the request
     * @param array  $headers the request headers
     * @param int    $timeOut the timeout in seconds for the request
     */
    public function openConnection($url, $method, $body, array $headers, $timeOut): void
    {
        $options = [
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_HTTPHEADER => $this->compileRequestHeaders($headers),
            CURLOPT_URL => $url,
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_TIMEOUT => $timeOut,
            CURLOPT_RETURNTRANSFER => true, // Return response as string
            CURLOPT_HEADER => true, // Enable header processing
            CURLOPT_SSL_VERIFYHOST => 2,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_CAINFO => __DIR__.'/certs/DigiCertHighAssuranceEVRootCA.pem',
        ];

        if ('GET' !== $method) {
            $options[CURLOPT_POSTFIELDS] = $body;
        }

        $this->facebookCurl->init();
        $this->facebookCurl->setoptArray($options);
    }

    /**
     * Closes an existing curl connection.
     */
    public function closeConnection(): void
    {
        $this->facebookCurl->close();
    }

    /**
     * Send the request and get the raw response from curl.
     */
    public function sendRequest(): void
    {
        $this->rawResponse = $this->facebookCurl->exec();
    }

    /**
     * Compiles the request headers into a curl-friendly format.
     *
     * @param array $headers the request headers
     *
     * @return array
     */
    public function compileRequestHeaders(array $headers)
    {
        $return = [];

        foreach ($headers as $key => $value) {
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
        $parts = explode("\r\n\r\n", $this->rawResponse);
        $rawBody = array_pop($parts);
        $rawHeaders = implode("\r\n\r\n", $parts);

        return [trim($rawHeaders), trim($rawBody)];
    }
}
