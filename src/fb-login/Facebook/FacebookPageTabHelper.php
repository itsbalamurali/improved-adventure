<?php





namespace Facebook;

/**
 * Class FacebookPageTabHelper.
 *
 * @author Fosco Marotto <fjm@fb.com>
 */
class FacebookPageTabHelper extends FacebookCanvasLoginHelper
{
    /**
     * @var null|array
     */
    protected $pageData;

    /**
     * Initialize the helper and process available signed request data.
     *
     * @param null|string $appId
     * @param null|string $appSecret
     */
    public function __construct($appId = null, $appSecret = null)
    {
        parent::__construct($appId, $appSecret);

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
     * Returns true if the page is liked by the user.
     *
     * @return bool
     */
    public function isLiked()
    {
        return true === $this->getPageData('liked');
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
