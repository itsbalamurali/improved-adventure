<?php





namespace Facebook;

/**
 * Class FacebookBatchResponse.
 */
class FacebookBatchResponse extends FacebookResponse implements \IteratorAggregate, \ArrayAccess
{
    /**
     * @var FacebookBatchRequest the original entity that made the batch request
     */
    protected $batchRequest;

    /**
     * @var array an array of FacebookResponse entities
     */
    protected $responses = [];

    /**
     * Creates a new Response entity.
     */
    public function __construct(FacebookBatchRequest $batchRequest, FacebookResponse $response)
    {
        $this->batchRequest = $batchRequest;

        $request = $response->getRequest();
        $body = $response->getBody();
        $httpStatusCode = $response->getHttpStatusCode();
        $headers = $response->getHeaders();
        parent::__construct($request, $body, $httpStatusCode, $headers);

        $responses = $response->getDecodedBody();
        $this->setResponses($responses);
    }

    /**
     * Returns an array of FacebookResponse entities.
     *
     * @return array
     */
    public function getResponses()
    {
        return $this->responses;
    }

    /**
     * The main batch response will be an array of requests so
     * we need to iterate over all the responses.
     */
    public function setResponses(array $responses): void
    {
        $this->responses = [];

        foreach ($responses as $key => $graphResponse) {
            $this->addResponse($key, $graphResponse);
        }
    }

    /**
     * Add a response to the list.
     *
     * @param int        $key
     * @param null|array $response
     */
    public function addResponse($key, $response): void
    {
        $originalRequestName = $this->batchRequest[$key]['name'] ?? $key;
        $originalRequest = $this->batchRequest[$key]['request'] ?? null;

        $httpResponseBody = $response['body'] ?? null;
        $httpResponseCode = $response['code'] ?? null;

        /** @TODO With PHP 5.5 support, this becomes array_column($response['headers'], 'value', 'name') */
        $httpResponseHeaders = isset($response['headers']) ? $this->normalizeBatchHeaders($response['headers']) : [];

        $this->responses[$originalRequestName] = new FacebookResponse(
            $originalRequest,
            $httpResponseBody,
            $httpResponseCode,
            $httpResponseHeaders
        );
    }

    public function getIterator()
    {
        return new \ArrayIterator($this->responses);
    }

    public function offsetSet($offset, $value): void
    {
        $this->addResponse($offset, $value);
    }

    public function offsetExists($offset)
    {
        return isset($this->responses[$offset]);
    }

    public function offsetUnset($offset): void
    {
        unset($this->responses[$offset]);
    }

    public function offsetGet($offset)
    {
        return $this->responses[$offset] ?? null;
    }

    /**
     * Converts the batch header array into a standard format.
     *
     * @TODO replace with array_column() when PHP 5.5 is supported.
     *
     * @return array
     */
    private function normalizeBatchHeaders(array $batchHeaders)
    {
        $headers = [];

        foreach ($batchHeaders as $header) {
            $headers[$header['name']] = $header['value'];
        }

        return $headers;
    }
}
