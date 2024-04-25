<?php





namespace Facebook;

/**
 * Class FacebookCanvasLoginHelper.
 *
 * @author Fosco Marotto <fjm@fb.com>
 * @author David Poll <depoll@fb.com>
 */
class FacebookCanvasLoginHelper extends FacebookSignedRequestFromInputHelper
{
    /**
     * Returns the app data value.
     *
     * @return null|mixed
     */
    public function getAppData()
    {
        return $this->signedRequest ? $this->signedRequest->get('app_data') : null;
    }

    /**
     * Get raw signed request from POST.
     *
     * @return null|string
     */
    public function getRawSignedRequest()
    {
        $rawSignedRequest = $this->getRawSignedRequestFromPost();
        if ($rawSignedRequest) {
            return $rawSignedRequest;
        }

        return null;
    }
}
