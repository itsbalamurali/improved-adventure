<?php





namespace Facebook\GraphNodes;

/**
 * Class GraphCoverPhoto.
 */
class GraphCoverPhoto extends GraphNode
{
    /**
     * Returns the id of cover if it exists.
     *
     * @return null|int
     */
    public function getId()
    {
        return $this->getField('id');
    }

    /**
     * Returns the source of cover if it exists.
     *
     * @return null|string
     */
    public function getSource()
    {
        return $this->getField('source');
    }

    /**
     * Returns the offset_x of cover if it exists.
     *
     * @return null|int
     */
    public function getOffsetX()
    {
        return $this->getField('offset_x');
    }

    /**
     * Returns the offset_y of cover if it exists.
     *
     * @return null|int
     */
    public function getOffsetY()
    {
        return $this->getField('offset_y');
    }
}
