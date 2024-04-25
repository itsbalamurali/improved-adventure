<?php





namespace Facebook\GraphNodes;

/**
 * Class GraphUser.
 */
class GraphUser extends GraphNode
{
    /**
     * @var array maps object key names to Graph object types
     */
    protected static $graphObjectMap = [
        'hometown' => '\Facebook\GraphNodes\GraphPage',
        'location' => '\Facebook\GraphNodes\GraphPage',
        'significant_other' => '\Facebook\GraphNodes\GraphUser',
        'picture' => '\Facebook\GraphNodes\GraphPicture',
    ];

    /**
     * Returns the ID for the user as a string if present.
     *
     * @return null|string
     */
    public function getId()
    {
        return $this->getField('id');
    }

    /**
     * Returns the name for the user as a string if present.
     *
     * @return null|string
     */
    public function getName()
    {
        return $this->getField('name');
    }

    /**
     * Returns the first name for the user as a string if present.
     *
     * @return null|string
     */
    public function getFirstName()
    {
        return $this->getField('first_name');
    }

    /**
     * Returns the middle name for the user as a string if present.
     *
     * @return null|string
     */
    public function getMiddleName()
    {
        return $this->getField('middle_name');
    }

    /**
     * Returns the last name for the user as a string if present.
     *
     * @return null|string
     */
    public function getLastName()
    {
        return $this->getField('last_name');
    }

    /**
     * Returns the email for the user as a string if present.
     *
     * @return null|string
     */
    public function getEmail()
    {
        return $this->getField('email');
    }

    /**
     * Returns the gender for the user as a string if present.
     *
     * @return null|string
     */
    public function getGender()
    {
        return $this->getField('gender');
    }

    /**
     * Returns the Facebook URL for the user as a string if available.
     *
     * @return null|string
     */
    public function getLink()
    {
        return $this->getField('link');
    }

    /**
     * Returns the users birthday, if available.
     *
     * @return null|Birthday
     */
    public function getBirthday()
    {
        return $this->getField('birthday');
    }

    /**
     * Returns the current location of the user as a GraphPage.
     *
     * @return null|GraphPage
     */
    public function getLocation()
    {
        return $this->getField('location');
    }

    /**
     * Returns the current location of the user as a GraphPage.
     *
     * @return null|GraphPage
     */
    public function getHometown()
    {
        return $this->getField('hometown');
    }

    /**
     * Returns the current location of the user as a GraphUser.
     *
     * @return null|GraphUser
     */
    public function getSignificantOther()
    {
        return $this->getField('significant_other');
    }

    /**
     * Returns the picture of the user as a GraphPicture.
     *
     * @return null|GraphPicture
     */
    public function getPicture()
    {
        return $this->getField('picture');
    }
}
