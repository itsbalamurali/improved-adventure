<?php





namespace Facebook\GraphNodes;

use Facebook\Exceptions\FacebookSDKException;

/**
 * Class GraphObjectFactory.
 *
 * @deprecated 5.0.0 GraphObjectFactory has been renamed to GraphNodeFactory
 *
 * @todo v6: Remove this class
 */
class GraphObjectFactory extends GraphNodeFactory
{
    /**
     * @const string The base graph object class.
     */
    public const BASE_GRAPH_NODE_CLASS = '\Facebook\GraphNodes\GraphObject';

    /**
     * @const string The base graph edge class.
     */
    public const BASE_GRAPH_EDGE_CLASS = '\Facebook\GraphNodes\GraphList';

    /**
     * Tries to convert a FacebookResponse entity into a GraphNode.
     *
     * @param null|string $subclassName the GraphNode sub class to cast to
     *
     * @return GraphNode
     *
     * @deprecated 5.0.0 GraphObjectFactory has been renamed to GraphNodeFactory
     */
    public function makeGraphObject($subclassName = null)
    {
        return $this->makeGraphNode($subclassName);
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
     * Tries to convert a FacebookResponse entity into a GraphEdge.
     *
     * @param null|string $subclassName the GraphNode sub class to cast the list items to
     * @param bool        $auto_prefix  toggle to auto-prefix the subclass name
     *
     * @return GraphEdge
     *
     * @deprecated 5.0.0 GraphObjectFactory has been renamed to GraphNodeFactory
     */
    public function makeGraphList($subclassName = null, $auto_prefix = true)
    {
        return $this->makeGraphEdge($subclassName, $auto_prefix);
    }
}
