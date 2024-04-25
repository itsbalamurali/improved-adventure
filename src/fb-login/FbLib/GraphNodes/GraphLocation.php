<?php





namespace Facebook\GraphNodes;

/**
 * Class GraphLocation.
 */
class GraphLocation extends GraphNode
{
    /**
     * Returns the street component of the location.
     *
     * @return null|string
     */
    public function getStreet()
    {
        return $this->getField('street');
    }

    /**
     * Returns the city component of the location.
     *
     * @return null|string
     */
    public function getCity()
    {
        return $this->getField('city');
    }

    /**
     * Returns the state component of the location.
     *
     * @return null|string
     */
    public function getState()
    {
        return $this->getField('state');
    }

    /**
     * Returns the country component of the location.
     *
     * @return null|string
     */
    public function getCountry()
    {
        return $this->getField('country');
    }

    /**
     * Returns the zipcode component of the location.
     *
     * @return null|string
     */
    public function getZip()
    {
        return $this->getField('zip');
    }

    /**
     * Returns the latitude component of the location.
     *
     * @return null|float
     */
    public function getLatitude()
    {
        return $this->getField('latitude');
    }

    /**
     * Returns the street component of the location.
     *
     * @return null|float
     */
    public function getLongitude()
    {
        return $this->getField('longitude');
    }
}
