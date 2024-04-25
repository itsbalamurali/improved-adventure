<?php





namespace Facebook;

/**
 * Class GraphLocation.
 *
 * @author Fosco Marotto <fjm@fb.com>
 * @author David Poll <depoll@fb.com>
 */
class GraphLocation extends GraphObject
{
    /**
     * Returns the street component of the location.
     *
     * @return null|string
     */
    public function getStreet()
    {
        return $this->getProperty('street');
    }

    /**
     * Returns the city component of the location.
     *
     * @return null|string
     */
    public function getCity()
    {
        return $this->getProperty('city');
    }

    /**
     * Returns the state component of the location.
     *
     * @return null|string
     */
    public function getState()
    {
        return $this->getProperty('state');
    }

    /**
     * Returns the country component of the location.
     *
     * @return null|string
     */
    public function getCountry()
    {
        return $this->getProperty('country');
    }

    /**
     * Returns the zipcode component of the location.
     *
     * @return null|string
     */
    public function getZip()
    {
        return $this->getProperty('zip');
    }

    /**
     * Returns the latitude component of the location.
     *
     * @return null|float
     */
    public function getLatitude()
    {
        return $this->getProperty('latitude');
    }

    /**
     * Returns the street component of the location.
     *
     * @return null|float
     */
    public function getLongitude()
    {
        return $this->getProperty('longitude');
    }
}
