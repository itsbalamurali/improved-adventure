<?php





namespace Facebook\HttpClients;

use Facebook\Exceptions\FacebookSDKException;
use Facebook\Http\GraphRawResponse;

class FacebookStreamHttpClient implements FacebookHttpClientInterface
{
    /**
     * @var FacebookStream procedural stream wrapper as object
     */
    protected $facebookStream;

    /**
     * @param null|FacebookStream procedural stream wrapper as object
     */
    public function __construct(?FacebookStream $facebookStream = null)
    {
        $this->facebookStream = $facebookStream ?: new FacebookStream();
    }

    public function send($url, $method, $body, array $headers, $timeOut)
    {
        $options = [
            'http' => [
                'method' => $method,
                'header' => $this->compileHeader($headers),
                'content' => $body,
                'timeout' => $timeOut,
                'ignore_errors' => true,
            ],
            'ssl' => [
                'verify_peer' => true,
                'verify_peer_name' => true,
                'allow_self_signed' => true, // All root certificates are self-signed
                'cafile' => __DIR__.'/certs/DigiCertHighAssuranceEVRootCA.pem',
            ],
        ];

        $this->facebookStream->streamContextCreate($options);
        $rawBody = $this->facebookStream->fileGetContents($url);
        $rawHeaders = $this->facebookStream->getResponseHeaders();

        if (false === $rawBody || empty($rawHeaders)) {
            throw new FacebookSDKException('Stream returned an empty response', 660);
        }

        $rawHeaders = implode("\r\n", $rawHeaders);

        return new GraphRawResponse($rawHeaders, $rawBody);
    }

    /**
     * Formats the headers for use in the stream wrapper.
     *
     * @param array $headers the request headers
     *
     * @return string
     */
    public function compileHeader(array $headers)
    {
        $header = [];
        foreach ($headers as $k => $v) {
            $header[] = $k.': '.$v;
        }

        return implode("\r\n", $header);
    }
}
