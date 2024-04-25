<?php





namespace Facebook\GraphNodes;

/**
 * Class GraphApplication.
 */
class GraphApplication extends GraphNode
{
    /**
     * Returns the ID for the application.
     *
     * @return null|string
     */
    public function getId()
    {
        return $this->getField('id');
    }
}
