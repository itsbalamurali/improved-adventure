<?php





namespace Facebook\Helpers;

/**
 * Class FacebookCanvasLoginHelper.
 */
class FacebookCanvasHelper extends FacebookSignedRequestFromInputHelper
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
        return $this->getRawSignedRequestFromPost() ?: null;
    }
}
