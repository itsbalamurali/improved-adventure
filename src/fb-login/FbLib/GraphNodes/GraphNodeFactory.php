<?php





namespace Facebook\GraphNodes;

use Facebook\Exceptions\FacebookSDKException;
use Facebook\FacebookResponse;

/**
 * Class GraphNodeFactory.
 */
class GraphNodeFactory
{
    /**
     * @const string The base graph object class.
     */
    public const BASE_GRAPH_NODE_CLASS = '\Facebook\GraphNodes\GraphNode';

    /**
     * @const string The base graph edge class.
     */
    public const BASE_GRAPH_EDGE_CLASS = '\Facebook\GraphNodes\GraphEdge';

    /**
     * @const string The graph object prefix.
     */
    public const BASE_GRAPH_OBJECT_PREFIX = '\Facebook\GraphNodes\\';

    /**
     * @var FacebookResponse the response entity from Graph
     */
    protected $response;

    /**
     * @var array the decoded body of the FacebookResponse entity from Graph
     */
    protected $decodedBody;

    /**
     * Init this Graph object.
     *
     * @param FacebookResponse $response the response entity from Graph
     */
    public function __construct(FacebookResponse $response)
    {
        $this->response = $response;
        $this->decodedBody = $response->getDecodedBody();
    }

    /**
     * Tries to convert a FacebookResponse entity into a GraphNode.
     *
     * @param null|string $subclassName the GraphNode sub class to cast to
     *
     * @return GraphNode
     *
     * @throws FacebookSDKException
     */
    public function makeGraphNode($subclassName = null)
    {
        $this->validateResponseAsArray();
        $this->validateResponseCastableAsGraphNode();

        return $this->castAsGraphNodeOrGraphEdge($this->decodedBody, $subclassName);
    }

    /**
     * Convenience method for creating a GraphAchievement collection.
     *
     * @return GraphAchievement
     *
     * @throws FacebookSDKException
     */
    public function makeGraphAchievement()
    {
        return $this->makeGraphNode(static::BASE_GRAPH_OBJECT_PREFIX.'GraphAchievement');
    }

    /**
     * Convenience method for creating a GraphAlbum collection.
     *
     * @return GraphAlbum
     *
     * @throws FacebookSDKException
     */
    public function makeGraphAlbum()
    {
        return $this->makeGraphNode(static::BASE_GRAPH_OBJECT_PREFIX.'GraphAlbum');
    }

    /**
     * Convenience method for creating a GraphPage collection.
     *
     * @return GraphPage
     *
     * @throws FacebookSDKException
     */
    public function makeGraphPage()
    {
        return $this->makeGraphNode(static::BASE_GRAPH_OBJECT_PREFIX.'GraphPage');
    }

    /**
     * Convenience method for creating a GraphSessionInfo collection.
     *
     * @return GraphSessionInfo
     *
     * @throws FacebookSDKException
     */
    public function makeGraphSessionInfo()
    {
        return $this->makeGraphNode(static::BASE_GRAPH_OBJECT_PREFIX.'GraphSessionInfo');
    }

    /**
     * Convenience method for creating a GraphUser collection.
     *
     * @return GraphUser
     *
     * @throws FacebookSDKException
     */
    public function makeGraphUser()
    {
        return $this->makeGraphNode(static::BASE_GRAPH_OBJECT_PREFIX.'GraphUser');
    }

    /**
     * Convenience method for creating a GraphEvent collection.
     *
     * @return GraphEvent
     *
     * @throws FacebookSDKException
     */
    public function makeGraphEvent()
    {
        return $this->makeGraphNode(static::BASE_GRAPH_OBJECT_PREFIX.'GraphEvent');
    }

    /**
     * Convenience method for creating a GraphGroup collection.
     *
     * @return GraphGroup
     *
     * @throws FacebookSDKException
     */
    public function makeGraphGroup()
    {
        return $this->makeGraphNode(static::BASE_GRAPH_OBJECT_PREFIX.'GraphGroup');
    }

    /**
     * Tries to convert a FacebookResponse entity into a GraphEdge.
     *
     * @param null|string $subclassName the GraphNode sub class to cast the list items to
     * @param bool        $auto_prefix  toggle to auto-prefix the subclass name
     *
     * @return GraphEdge
     *
     * @throws FacebookSDKException
     */
    public function makeGraphEdge($subclassName = null, $auto_prefix = true)
    {
        $this->validateResponseAsArray();
        $this->validateResponseCastableAsGraphEdge();

        if ($subclassName && $auto_prefix) {
            $subclassName = static::BASE_GRAPH_OBJECT_PREFIX.$subclassName;
        }

        return $this->castAsGraphNodeOrGraphEdge($this->decodedBody, $subclassName);
    }

    /**
     * Validates the decoded body.
     *
     * @throws FacebookSDKException
     */
    public function validateResponseAsArray(): void
    {
        if (!\is_array($this->decodedBody)) {
            throw new FacebookSDKException('Unable to get response from Graph as array.', 620);
        }
    }

    /**
     * Validates that the return data can be cast as a GraphNode.
     *
     * @throws FacebookSDKException
     */
    public function validateResponseCastableAsGraphNode(): void
    {
        if (isset($this->decodedBody['data']) && static::isCastableAsGraphEdge($this->decodedBody['data'])) {
            throw new FacebookSDKException(
                'Unable to convert response from Graph to a GraphNode because the response looks like a GraphEdge. Try using GraphNodeFactory::makeGraphEdge() instead.',
                620
            );
        }
    }

    /**
     * Validates that the return data can be cast as a GraphEdge.
     *
     * @throws FacebookSDKException
     */
    public function validateResponseCastableAsGraphEdge(): void
    {
        if (!(isset($this->decodedBody['data']) && static::isCastableAsGraphEdge($this->decodedBody['data']))) {
            throw new FacebookSDKException(
                'Unable to convert response from Graph to a GraphEdge because the response does not look like a GraphEdge. Try using GraphNodeFactory::makeGraphNode() instead.',
                620
            );
        }
    }

    /**
     * Safely instantiates a GraphNode of $subclassName.
     *
     * @param array       $data         the array of data to iterate over
     * @param null|string $subclassName the subclass to cast this collection to
     *
     * @return GraphNode
     *
     * @throws FacebookSDKException
     */
    public function safelyMakeGraphNode(array $data, $subclassName = null)
    {
        $subclassName = $subclassName ?: static::BASE_GRAPH_NODE_CLASS;
        static::validateSubclass($subclassName);

        // Remember the parent node ID
        $parentNodeId = $data['id'] ?? null;

        $items = [];

        foreach ($data as $k => $v) {
            // Array means could be recurable
            if (\is_array($v)) {
                // Detect any smart-casting from the $graphObjectMap array.
                // This is always empty on the GraphNode collection, but subclasses can define
                // their own array of smart-casting types.
                $graphObjectMap = $subclassName::getObjectMap();
                $objectSubClass = $graphObjectMap[$k]
                    ?? null;

                // Could be a GraphEdge or GraphNode
                $items[$k] = $this->castAsGraphNodeOrGraphEdge($v, $objectSubClass, $k, $parentNodeId);
            } else {
                $items[$k] = $v;
            }
        }

        return new $subclassName($items);
    }

    /**
     * Takes an array of values and determines how to cast each node.
     *
     * @param array       $data         the array of data to iterate over
     * @param null|string $subclassName the subclass to cast this collection to
     * @param null|string $parentKey    the key of this data (Graph edge)
     * @param null|string $parentNodeId the parent Graph node ID
     *
     * @return GraphEdge|GraphNode
     *
     * @throws FacebookSDKException
     */
    public function castAsGraphNodeOrGraphEdge(array $data, $subclassName = null, $parentKey = null, $parentNodeId = null)
    {
        if (isset($data['data'])) {
            // Create GraphEdge
            if (static::isCastableAsGraphEdge($data['data'])) {
                return $this->safelyMakeGraphEdge($data, $subclassName, $parentKey, $parentNodeId);
            }
            // Sometimes Graph is a weirdo and returns a GraphNode under the "data" key
            $data = $data['data'];
        }

        // Create GraphNode
        return $this->safelyMakeGraphNode($data, $subclassName);
    }

    /**
     * Return an array of GraphNode's.
     *
     * @param array       $data         the array of data to iterate over
     * @param null|string $subclassName the GraphNode subclass to cast each item in the list to
     * @param null|string $parentKey    the key of this data (Graph edge)
     * @param null|string $parentNodeId the parent Graph node ID
     *
     * @return GraphEdge
     *
     * @throws FacebookSDKException
     */
    public function safelyMakeGraphEdge(array $data, $subclassName = null, $parentKey = null, $parentNodeId = null)
    {
        if (!isset($data['data'])) {
            throw new FacebookSDKException('Cannot cast data to GraphEdge. Expected a "data" key.', 620);
        }

        $dataList = [];
        foreach ($data['data'] as $graphNode) {
            $dataList[] = $this->safelyMakeGraphNode($graphNode, $subclassName);
        }

        $metaData = $this->getMetaData($data);

        // We'll need to make an edge endpoint for this in case it's a GraphEdge (for cursor pagination)
        $parentGraphEdgeEndpoint = $parentNodeId && $parentKey ? '/'.$parentNodeId.'/'.$parentKey : null;
        $className = static::BASE_GRAPH_EDGE_CLASS;

        return new $className($this->response->getRequest(), $dataList, $metaData, $parentGraphEdgeEndpoint, $subclassName);
    }

    /**
     * Get the meta data from a list in a Graph response.
     *
     * @param array $data the Graph response
     *
     * @return array
     */
    public function getMetaData(array $data)
    {
        unset($data['data']);

        return $data;
    }

    /**
     * Determines whether or not the data should be cast as a GraphEdge.
     *
     * @return bool
     */
    public static function isCastableAsGraphEdge(array $data)
    {
        if ([] === $data) {
            return true;
        }

        // Checks for a sequential numeric array which would be a GraphEdge
        return array_keys($data) === range(0, \count($data) - 1);
    }

    /**
     * Ensures that the subclass in question is valid.
     *
     * @param string $subclassName the GraphNode subclass to validate
     *
     * @throws FacebookSDKException
     */
    public static function validateSubclass($subclassName): void
    {
        if ($subclassName === static::BASE_GRAPH_NODE_CLASS || is_subclass_of($subclassName, static::BASE_GRAPH_NODE_CLASS)) {
            return;
        }

        throw new FacebookSDKException('The given subclass "'.$subclassName.'" is not valid. Cannot cast to an object that is not a GraphNode subclass.', 620);
    }
}
