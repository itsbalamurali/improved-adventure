<?php





namespace Facebook\HttpClients;

/**
 * Class FacebookCurl.
 *
 * Abstraction for the procedural curl elements so that curl can be mocked and the implementation can be tested.
 */
class FacebookCurl
{
    /**
     * @var resource Curl resource instance
     */
    protected $curl;

    /**
     * Make a new curl reference instance.
     */
    public function init(): void
    {
        $this->curl = curl_init();
    }

    /**
     * Set a curl option.
     */
    public function setopt($key, $value): void
    {
        curl_setopt($this->curl, $key, $value);
    }

    /**
     * Set an array of options to a curl resource.
     */
    public function setoptArray(array $options): void
    {
        curl_setopt_array($this->curl, $options);
    }

    /**
     * Send a curl request.
     *
     * @return mixed
     */
    public function exec()
    {
        return curl_exec($this->curl);
    }

    /**
     * Return the curl error number.
     *
     * @return int
     */
    public function errno()
    {
        return curl_errno($this->curl);
    }

    /**
     * Return the curl error message.
     *
     * @return string
     */
    public function error()
    {
        return curl_error($this->curl);
    }

    /**
     * Get info from a curl reference.
     *
     * @return mixed
     */
    public function getinfo($type)
    {
        return curl_getinfo($this->curl, $type);
    }

    /**
     * Get the currently installed curl version.
     *
     * @return array
     */
    public function version()
    {
        return curl_version();
    }

    /**
     * Close the resource connection to curl.
     */
    public function close(): void
    {
        curl_close($this->curl);
    }
}
