<?php





namespace Facebook\Http;

/**
 * Class RequestBodyUrlEncoded.
 */
class RequestBodyUrlEncoded implements RequestBodyInterface
{
    /**
     * @var array the parameters to send with this request
     */
    protected $params = [];

    /**
     * Creates a new GraphUrlEncodedBody entity.
     */
    public function __construct(array $params)
    {
        $this->params = $params;
    }

    public function getBody()
    {
        return http_build_query($this->params, null, '&');
    }
}
