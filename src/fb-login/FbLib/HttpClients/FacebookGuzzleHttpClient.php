<?php





namespace Facebook\HttpClients;

use Facebook\Exceptions\FacebookSDKException;
use Facebook\Http\GraphRawResponse;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Message\ResponseInterface;
use GuzzleHttp\Ring\Exception\RingException;

class FacebookGuzzleHttpClient implements FacebookHttpClientInterface
{
    /**
     * @var Client the Guzzle client
     */
    protected $guzzleClient;

    /**
     * @param null|Client the Guzzle client
     */
    public function __construct(?Client $guzzleClient = null)
    {
        $this->guzzleClient = $guzzleClient ?: new Client();
    }

    public function send($url, $method, $body, array $headers, $timeOut)
    {
        $options = [
            'headers' => $headers,
            'body' => $body,
            'timeout' => $timeOut,
            'connect_timeout' => 10,
            'verify' => __DIR__.'/certs/DigiCertHighAssuranceEVRootCA.pem',
        ];
        $request = $this->guzzleClient->createRequest($method, $url, $options);

        try {
            $rawResponse = $this->guzzleClient->send($request);
        } catch (RequestException $e) {
            $rawResponse = $e->getResponse();

            if ($e->getPrevious() instanceof RingException || !$rawResponse instanceof ResponseInterface) {
                throw new FacebookSDKException($e->getMessage(), $e->getCode());
            }
        }

        $rawHeaders = $this->getHeadersAsString($rawResponse);
        $rawBody = $rawResponse->getBody();
        $httpStatusCode = $rawResponse->getStatusCode();

        return new GraphRawResponse($rawHeaders, $rawBody, $httpStatusCode);
    }

    /**
     * Returns the Guzzle array of headers as a string.
     *
     * @param ResponseInterface $response the Guzzle response
     *
     * @return string
     */
    public function getHeadersAsString(ResponseInterface $response)
    {
        $headers = $response->getHeaders();
        $rawHeaders = [];
        foreach ($headers as $name => $values) {
            $rawHeaders[] = $name.': '.implode(', ', $values);
        }

        return implode("\r\n", $rawHeaders);
    }
}
