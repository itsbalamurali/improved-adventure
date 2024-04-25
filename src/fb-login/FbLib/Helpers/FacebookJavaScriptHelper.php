<?php





namespace Facebook\Helpers;

/**
 * Class FacebookJavaScriptLoginHelper.
 */
class FacebookJavaScriptHelper extends FacebookSignedRequestFromInputHelper
{
    /**
     * Get raw signed request from the cookie.
     *
     * @return null|string
     */
    public function getRawSignedRequest()
    {
        return $this->getRawSignedRequestFromCookie();
    }
}
