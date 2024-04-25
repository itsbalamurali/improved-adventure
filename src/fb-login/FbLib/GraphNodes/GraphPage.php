<?php





namespace Facebook\GraphNodes;

/**
 * Class GraphPage.
 */
class GraphPage extends GraphNode
{
    /**
     * @var array maps object key names to Graph object types
     */
    protected static $graphObjectMap = [
        'best_page' => '\Facebook\GraphNodes\GraphPage',
        'global_brand_parent_page' => '\Facebook\GraphNodes\GraphPage',
        'location' => '\Facebook\GraphNodes\GraphLocation',
        'cover' => '\Facebook\GraphNodes\GraphCoverPhoto',
        'picture' => '\Facebook\GraphNodes\GraphPicture',
    ];

    /**
     * Returns the ID for the user's page as a string if present.
     *
     * @return null|string
     */
    public function getId()
    {
        return $this->getField('id');
    }

    /**
     * Returns the Category for the user's page as a string if present.
     *
     * @return null|string
     */
    public function getCategory()
    {
        return $this->getField('category');
    }

    /**
     * Returns the Name of the user's page as a string if present.
     *
     * @return null|string
     */
    public function getName()
    {
        return $this->getField('name');
    }

    /**
     * Returns the best available Page on Facebook.
     *
     * @return null|GraphPage
     */
    public function getBestPage()
    {
        return $this->getField('best_page');
    }

    /**
     * Returns the brand's global (parent) Page.
     *
     * @return null|GraphPage
     */
    public function getGlobalBrandParentPage()
    {
        return $this->getField('global_brand_parent_page');
    }

    /**
     * Returns the location of this place.
     *
     * @return null|GraphLocation
     */
    public function getLocation()
    {
        return $this->getField('location');
    }

    /**
     * Returns CoverPhoto of the Page.
     *
     * @return null|GraphCoverPhoto
     */
    public function getCover()
    {
        return $this->getField('cover');
    }

    /**
     * Returns Picture of the Page.
     *
     * @return null|GraphPicture
     */
    public function getPicture()
    {
        return $this->getField('picture');
    }

    /**
     * Returns the page access token for the admin user.
     *
     * Only available in the `/me/accounts` context.
     *
     * @return null|string
     */
    public function getAccessToken()
    {
        return $this->getField('access_token');
    }

    /**
     * Returns the roles of the page admin user.
     *
     * Only available in the `/me/accounts` context.
     *
     * @return null|array
     */
    public function getPerms()
    {
        return $this->getField('perms');
    }
}
