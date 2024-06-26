<?php





namespace Facebook;

/**
 * Class FacebookResponse.
 *
 * @author Fosco Marotto <fjm@fb.com>
 * @author David Poll <depoll@fb.com>
 */
class FacebookResponse
{
    /**
     * @var FacebookRequest The request which produced this response
     */
    private $request;

    /**
     * @var array The decoded response from the Graph API
     */
    private $responseData;

    /**
     * @var string The raw response from the Graph API
     */
    private $rawResponse;

    /**
     * @var bool Indicates whether sent ETag matched the one on the FB side
     */
    private $etagHit;

    /**
     * @var string ETag received with the response. `null` in case of ETag hit.
     */
    private $etag;

    /**
     * Creates a FacebookResponse object for a given request and response.
     *
     * @param FacebookRequest $request
     * @param array           $responseData JSON Decoded response data
     * @param string          $rawResponse  Raw string response
     * @param bool            $etagHit      Indicates whether sent ETag matched the one on the FB side
     * @param null|string     $etag         ETag received with the response. `null` in case of ETag hit.
     */
    public function __construct($request, $responseData, $rawResponse, $etagHit = false, $etag = null)
    {
        $this->request = $request;
        $this->responseData = $responseData;
        $this->rawResponse = $rawResponse;
        $this->etagHit = $etagHit;
        $this->etag = $etag;
    }

    /**
     * Returns the request which produced this response.
     *
     * @return FacebookRequest
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * Returns the decoded response data.
     *
     * @return array
     */
    public function getResponse()
    {
        return $this->responseData;
    }

    /**
     * Returns the raw response.
     *
     * @return string
     */
    public function getRawResponse()
    {
        return $this->rawResponse;
    }

    /**
     * Returns true if ETag matched the one sent with a request.
     *
     * @return bool
     */
    public function isETagHit()
    {
        return $this->etagHit;
    }

    /**
     * Returns the ETag.
     *
     * @return string
     */
    public function getETag()
    {
        return $this->etag;
    }

    /**
     * Gets the result as a GraphObject.  If a type is specified, returns the
     *   strongly-typed subclass of GraphObject for the data.
     *
     * @param string $type
     *
     * @return mixed
     */
    public function getGraphObject($type = 'Facebook\GraphObject')
    {
        return (new GraphObject($this->responseData))->cast($type);
    }

    /**
     * Returns an array of GraphObject returned by the request.  If a type is
     * specified, returns the strongly-typed subclass of GraphObject for the data.
     *
     * @param string $type
     *
     * @return mixed
     */
    public function getGraphObjectList($type = 'Facebook\GraphObject')
    {
        $out = [];
        $data = $this->responseData->data;
        $dataLength = \count($data);
        for ($i = 0; $i < $dataLength; ++$i) {
            $out[] = (new GraphObject($data[$i]))->cast($type);
        }

        return $out;
    }

    /**
     * If this response has paginated data, returns the FacebookRequest for the
     *   next page, or null.
     *
     * @return null|FacebookRequest
     */
    public function getRequestForNextPage()
    {
        return $this->handlePagination('next');
    }

    /**
     * If this response has paginated data, returns the FacebookRequest for the
     *   previous page, or null.
     *
     * @return null|FacebookRequest
     */
    public function getRequestForPreviousPage()
    {
        return $this->handlePagination('previous');
    }

    /**
     * Returns the FacebookRequest for the previous or next page, or null.
     *
     * @param string $direction
     *
     * @return null|FacebookRequest
     */
    private function handlePagination($direction)
    {
        if (isset($this->responseData->paging->{$direction})) {
            $url = parse_url($this->responseData->paging->{$direction});
            parse_str($url['query'], $params);

            if (isset($params['type']) && str_contains($this->request->getPath(), $params['type'])) {
                unset($params['type']);
            }

            return new FacebookRequest(
                $this->request->getSession(),
                $this->request->getMethod(),
                $this->request->getPath(),
                $params
            );
        }

        return null;
    }
}
