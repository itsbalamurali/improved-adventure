<?php





namespace Facebook;

/**
 * Class GraphUserPage.
 *
 * @author Artur Luiz <artur@arturluiz.com.br>
 */
class GraphUserPage extends GraphObject
{
    /**
     * Returns the ID for the user's page as a string if present.
     *
     * @return null|string
     */
    public function getId()
    {
        return $this->getProperty('id');
    }

    /**
     * Returns the Category for the user's page as a string if present.
     *
     * @return null|string
     */
    public function getCategory()
    {
        return $this->getProperty('category');
    }

    /**
     * Returns the Name of the user's page as a string if present.
     *
     * @return null|string
     */
    public function getName()
    {
        return $this->getProperty('name');
    }

    /**
     * Returns the Access Token used to access the user's page as a string if present.
     *
     * @return null|string
     */
    public function getAccessToken()
    {
        return $this->getProperty('access_token');
    }

    /**
     * Returns the Permissions for the user's page as an array if present.
     *
     * @return null|array
     */
    public function getPermissions()
    {
        return $this->getProperty('perms');
    }
}
