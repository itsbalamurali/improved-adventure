<?php





namespace Facebook\Helpers;

use Facebook\FacebookApp;
use Facebook\FacebookClient;

/**
 * Class FacebookPageTabHelper.
 */
class FacebookPageTabHelper extends FacebookCanvasHelper
{
    /**
     * @var null|array
     */
    protected $pageData;

    /**
     * Initialize the helper and process available signed request data.
     *
     * @param FacebookApp    $app          the FacebookApp entity
     * @param FacebookClient $client       the client to make HTTP requests
     * @param null|string    $graphVersion the version of Graph to use
     */
    public function __construct(FacebookApp $app, FacebookClient $client, $graphVersion = null)
    {
        parent::__construct($app, $client, $graphVersion);

        if (!$this->signedRequest) {
            return;
        }

        $this->pageData = $this->signedRequest->get('page');
    }

    /**
     * Returns a value from the page data.
     *
     * @param string     $key
     * @param null|mixed $default
     *
     * @return null|mixed
     */
    public function getPageData($key, $default = null)
    {
        if (isset($this->pageData[$key])) {
            return $this->pageData[$key];
        }

        return $default;
    }

    /**
     * Returns true if the user is an admin.
     *
     * @return bool
     */
    public function isAdmin()
    {
        return true === $this->getPageData('admin');
    }

    /**
     * Returns the page id if available.
     *
     * @return null|string
     */
    public function getPageId()
    {
        return $this->getPageData('id');
    }
}
