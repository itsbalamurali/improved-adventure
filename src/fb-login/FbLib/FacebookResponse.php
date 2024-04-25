<?php





namespace Facebook;

use Facebook\Exceptions\FacebookResponseException;
use Facebook\Exceptions\FacebookSDKException;
use Facebook\GraphNodes\GraphAlbum;
use Facebook\GraphNodes\GraphEdge;
use Facebook\GraphNodes\GraphEvent;
use Facebook\GraphNodes\GraphGroup;
use Facebook\GraphNodes\GraphList;
use Facebook\GraphNodes\GraphNode;
use Facebook\GraphNodes\GraphNodeFactory;
use Facebook\GraphNodes\GraphObject;
use Facebook\GraphNodes\GraphPage;
use Facebook\GraphNodes\GraphSessionInfo;
use Facebook\GraphNodes\GraphUser;

/**
 * Class FacebookResponse.
 */
class FacebookResponse
{
    /**
     * @var int the HTTP status code response from Graph
     */
    protected $httpStatusCode;

    /**
     * @var array the headers returned from Graph
     */
    protected $headers;

    /**
     * @var string the raw body of the response from Graph
     */
    protected $body;

    /**
     * @var array the decoded body of the Graph response
     */
    protected $decodedBody = [];

    /**
     * @var FacebookRequest the original request that returned this response
     */
    protected $request;

    /**
     * @var FacebookSDKException the exception thrown by this request
     */
    protected $thrownException;

    /**
     * Creates a new Response entity.
     *
     * @param null|string $body
     * @param null|int    $httpStatusCode
     * @param null|array  $headers
     */
    public function __construct(FacebookRequest $request, $body = null, $httpStatusCode = null, array $headers = [])
    {
        $this->request = $request;
        $this->body = $body;
        $this->httpStatusCode = $httpStatusCode;
        $this->headers = $headers;

        $this->decodeBody();
    }

    /**
     * Return the original request that returned this response.
     *
     * @return FacebookRequest
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * Return the FacebookApp entity used for this response.
     *
     * @return FacebookApp
     */
    public function getApp()
    {
        return $this->request->getApp();
    }

    /**
     * Return the access token that was used for this response.
     *
     * @return null|string
     */
    public function getAccessToken()
    {
        return $this->request->getAccessToken();
    }

    /**
     * Return the HTTP status code for this response.
     *
     * @return int
     */
    public function getHttpStatusCode()
    {
        return $this->httpStatusCode;
    }

    /**
     * Return the HTTP headers for this response.
     *
     * @return array
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * Return the raw body response.
     *
     * @return string
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * Return the decoded body response.
     *
     * @return array
     */
    public function getDecodedBody()
    {
        return $this->decodedBody;
    }

    /**
     * Get the app secret proof that was used for this response.
     *
     * @return null|string
     */
    public function getAppSecretProof()
    {
        return $this->request->getAppSecretProof();
    }

    /**
     * Get the ETag associated with the response.
     *
     * @return null|string
     */
    public function getETag()
    {
        return $this->headers['ETag'] ?? null;
    }

    /**
     * Get the version of Graph that returned this response.
     *
     * @return null|string
     */
    public function getGraphVersion()
    {
        return $this->headers['Facebook-API-Version'] ?? null;
    }

    /**
     * Returns true if Graph returned an error message.
     *
     * @return bool
     */
    public function isError()
    {
        return isset($this->decodedBody['error']);
    }

    /**
     * Throws the exception.
     *
     * @throws FacebookSDKException
     */
    public function throwException(): void
    {
        throw $this->thrownException;
    }

    /**
     * Instantiates an exception to be thrown later.
     */
    public function makeException(): void
    {
        $this->thrownException = FacebookResponseException::create($this);
    }

    /**
     * Returns the exception that was thrown for this request.
     *
     * @return null|FacebookResponseException
     */
    public function getThrownException()
    {
        return $this->thrownException;
    }

    /**
     * Convert the raw response into an array if possible.
     *
     * Graph will return 2 types of responses:
     * - JSON(P)
     *    Most responses from Graph are JSON(P)
     * - application/x-www-form-urlencoded key/value pairs
     *    Happens on the `/oauth/access_token` endpoint when exchanging
     *    a short-lived access token for a long-lived access token
     * - And sometimes nothing :/ but that'd be a bug.
     */
    public function decodeBody(): void
    {
        $this->decodedBody = json_decode($this->body, true);

        if (null === $this->decodedBody) {
            $this->decodedBody = [];
            parse_str($this->body, $this->decodedBody);
        } elseif (\is_bool($this->decodedBody)) {
            // Backwards compatibility for Graph < 2.1.
            // Mimics 2.1 responses.
            // @TODO Remove this after Graph 2.0 is no longer supported
            $this->decodedBody = ['success' => $this->decodedBody];
        } elseif (is_numeric($this->decodedBody)) {
            $this->decodedBody = ['id' => $this->decodedBody];
        }

        if (!\is_array($this->decodedBody)) {
            $this->decodedBody = [];
        }

        if ($this->isError()) {
            $this->makeException();
        }
    }

    /**
     * Instantiate a new GraphObject from response.
     *
     * @param null|string $subclassName the GraphNode subclass to cast to
     *
     * @return GraphObject
     *
     * @throws FacebookSDKException
     *
     * @deprecated 5.0.0 getGraphObject() has been renamed to getGraphNode()
     *
     * @todo v6: Remove this method
     */
    public function getGraphObject($subclassName = null)
    {
        return $this->getGraphNode($subclassName);
    }

    /**
     * Instantiate a new GraphNode from response.
     *
     * @param null|string $subclassName the GraphNode subclass to cast to
     *
     * @return GraphNode
     *
     * @throws FacebookSDKException
     */
    public function getGraphNode($subclassName = null)
    {
        $factory = new GraphNodeFactory($this);

        return $factory->makeGraphNode($subclassName);
    }

    /**
     * Convenience method for creating a GraphAlbum collection.
     *
     * @return GraphAlbum
     *
     * @throws FacebookSDKException
     */
    public function getGraphAlbum()
    {
        $factory = new GraphNodeFactory($this);

        return $factory->makeGraphAlbum();
    }

    /**
     * Convenience method for creating a GraphPage collection.
     *
     * @return GraphPage
     *
     * @throws FacebookSDKException
     */
    public function getGraphPage()
    {
        $factory = new GraphNodeFactory($this);

        return $factory->makeGraphPage();
    }

    /**
     * Convenience method for creating a GraphSessionInfo collection.
     *
     * @return GraphSessionInfo
     *
     * @throws FacebookSDKException
     */
    public function getGraphSessionInfo()
    {
        $factory = new GraphNodeFactory($this);

        return $factory->makeGraphSessionInfo();
    }

    /**
     * Convenience method for creating a GraphUser collection.
     *
     * @return GraphUser
     *
     * @throws FacebookSDKException
     */
    public function getGraphUser()
    {
        $factory = new GraphNodeFactory($this);

        return $factory->makeGraphUser();
    }

    /**
     * Convenience method for creating a GraphEvent collection.
     *
     * @return GraphEvent
     *
     * @throws FacebookSDKException
     */
    public function getGraphEvent()
    {
        $factory = new GraphNodeFactory($this);

        return $factory->makeGraphEvent();
    }

    /**
     * Convenience method for creating a GraphGroup collection.
     *
     * @return GraphGroup
     *
     * @throws FacebookSDKException
     */
    public function getGraphGroup()
    {
        $factory = new GraphNodeFactory($this);

        return $factory->makeGraphGroup();
    }

    /**
     * Instantiate a new GraphList from response.
     *
     * @param null|string $subclassName the GraphNode subclass to cast list items to
     * @param bool        $auto_prefix  toggle to auto-prefix the subclass name
     *
     * @return GraphList
     *
     * @throws FacebookSDKException
     *
     * @deprecated 5.0.0 getGraphList() has been renamed to getGraphEdge()
     *
     * @todo v6: Remove this method
     */
    public function getGraphList($subclassName = null, $auto_prefix = true)
    {
        return $this->getGraphEdge($subclassName, $auto_prefix);
    }

    /**
     * Instantiate a new GraphEdge from response.
     *
     * @param null|string $subclassName the GraphNode subclass to cast list items to
     * @param bool        $auto_prefix  toggle to auto-prefix the subclass name
     *
     * @return GraphEdge
     *
     * @throws FacebookSDKException
     */
    public function getGraphEdge($subclassName = null, $auto_prefix = true)
    {
        $factory = new GraphNodeFactory($this);

        return $factory->makeGraphEdge($subclassName, $auto_prefix);
    }
}
