<?php





namespace Facebook;

/**
 * Class GraphUser.
 *
 * @author Fosco Marotto <fjm@fb.com>
 * @author David Poll <depoll@fb.com>
 */
class GraphUser extends GraphObject
{
    /**
     * Returns the ID for the user as a string if present.
     *
     * @return null|string
     */
    public function getId()
    {
        return $this->getProperty('id');
    }

    /**
     * Returns the name for the user as a string if present.
     *
     * @return null|string
     */
    public function getName()
    {
        return $this->getProperty('name');
    }

    public function getEmail()
    {
        return $this->getProperty('email');
    }

    /**
     * Returns the first name for the user as a string if present.
     *
     * @return null|string
     */
    public function getFirstName()
    {
        return $this->getProperty('first_name');
    }

    /**
     * Returns the middle name for the user as a string if present.
     *
     * @return null|string
     */
    public function getMiddleName()
    {
        return $this->getProperty('middle_name');
    }

    /**
     * Returns the last name for the user as a string if present.
     *
     * @return null|string
     */
    public function getLastName()
    {
        return $this->getProperty('last_name');
    }

    /**
     * Returns the gender for the user as a string if present.
     *
     * @return null|string
     */
    public function getGender()
    {
        return $this->getProperty('gender');
    }

    /**
     * Returns the Facebook URL for the user as a string if available.
     *
     * @return null|string
     */
    public function getLink()
    {
        return $this->getProperty('link');
    }

    /**
     * Returns the users birthday, if available.
     *
     * @return null|\DateTime
     */
    public function getBirthday()
    {
        $value = $this->getProperty('birthday');
        if ($value) {
            return new \DateTime($value);
        }

        return null;
    }

    /**
     * Returns the current location of the user as a FacebookGraphLocation
     *   if available.
     *
     * @return null|GraphLocation
     */
    public function getLocation()
    {
        return $this->getProperty('location', GraphLocation::className());
    }
}
