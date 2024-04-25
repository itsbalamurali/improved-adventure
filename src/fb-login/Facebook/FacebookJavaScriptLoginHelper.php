<?php





namespace Facebook;

/**
 * Class FacebookJavaScriptLoginHelper.
 *
 * @author Fosco Marotto <fjm@fb.com>
 * @author David Poll <depoll@fb.com>
 */
class FacebookJavaScriptLoginHelper extends FacebookSignedRequestFromInputHelper
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
