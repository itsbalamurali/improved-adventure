<?php





namespace Facebook\HttpClients;

use Facebook\Exceptions\FacebookSDKException;
use Facebook\Http\GraphRawResponse;

/**
 * Interface FacebookHttpClientInterface.
 */
interface FacebookHttpClientInterface
{
    /**
     * Sends a request to the server and returns the raw response.
     *
     * @param string $url     the endpoint to send the request to
     * @param string $method  the request method
     * @param string $body    the body of the request
     * @param array  $headers the request headers
     * @param int    $timeOut the timeout in seconds for the request
     *
     * @return GraphRawResponse raw response from the server
     *
     * @throws FacebookSDKException
     */
    public function send($url, $method, $body, array $headers, $timeOut);
}
