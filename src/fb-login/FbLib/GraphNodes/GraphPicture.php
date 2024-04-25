<?php





namespace Facebook\GraphNodes;

/**
 * Class GraphPicture.
 */
class GraphPicture extends GraphNode
{
    /**
     * Returns true if user picture is silhouette.
     *
     * @return null|bool
     */
    public function isSilhouette()
    {
        return $this->getField('is_silhouette');
    }

    /**
     * Returns the url of user picture if it exists.
     *
     * @return null|string
     */
    public function getUrl()
    {
        return $this->getField('url');
    }

    /**
     * Returns the width of user picture if it exists.
     *
     * @return null|int
     */
    public function getWidth()
    {
        return $this->getField('width');
    }

    /**
     * Returns the height of user picture if it exists.
     *
     * @return null|int
     */
    public function getHeight()
    {
        return $this->getField('height');
    }
}
