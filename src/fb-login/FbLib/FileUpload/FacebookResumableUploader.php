<?php





namespace Facebook\FileUpload;

use Facebook\Authentication\AccessToken;
use Facebook\Exceptions\FacebookResponseException;
use Facebook\Exceptions\FacebookResumableUploadException;
use Facebook\Exceptions\FacebookSDKException;
use Facebook\FacebookApp;
use Facebook\FacebookClient;
use Facebook\FacebookRequest;

/**
 * Class FacebookResumableUploader.
 */
class FacebookResumableUploader
{
    /**
     * @var FacebookApp
     */
    protected $app;

    /**
     * @var string
     */
    protected $accessToken;

    /**
     * @var FacebookClient the Facebook client service
     */
    protected $client;

    /**
     * @var string graph version to use for this request
     */
    protected $graphVersion;

    /**
     * @param null|AccessToken|string $accessToken
     * @param string                  $graphVersion
     */
    public function __construct(FacebookApp $app, FacebookClient $client, $accessToken, $graphVersion)
    {
        $this->app = $app;
        $this->client = $client;
        $this->accessToken = $accessToken;
        $this->graphVersion = $graphVersion;
    }

    /**
     * Upload by chunks - start phase.
     *
     * @param string $endpoint
     *
     * @return FacebookTransferChunk
     *
     * @throws FacebookSDKException
     */
    public function start($endpoint, FacebookFile $file)
    {
        $params = [
            'upload_phase' => 'start',
            'file_size' => $file->getSize(),
        ];
        $response = $this->sendUploadRequest($endpoint, $params);

        return new FacebookTransferChunk($file, $response['upload_session_id'], $response['video_id'], $response['start_offset'], $response['end_offset']);
    }

    /**
     * Upload by chunks - transfer phase.
     *
     * @param string $endpoint
     * @param bool   $allowToThrow
     *
     * @return FacebookTransferChunk
     *
     * @throws FacebookResponseException
     */
    public function transfer($endpoint, FacebookTransferChunk $chunk, $allowToThrow = false)
    {
        $params = [
            'upload_phase' => 'transfer',
            'upload_session_id' => $chunk->getUploadSessionId(),
            'start_offset' => $chunk->getStartOffset(),
            'video_file_chunk' => $chunk->getPartialFile(),
        ];

        try {
            $response = $this->sendUploadRequest($endpoint, $params);
        } catch (FacebookResponseException $e) {
            $preException = $e->getPrevious();
            if ($allowToThrow || !$preException instanceof FacebookResumableUploadException) {
                throw $e;
            }

            // Return the same chunk entity so it can be retried.
            return $chunk;
        }

        return new FacebookTransferChunk($chunk->getFile(), $chunk->getUploadSessionId(), $chunk->getVideoId(), $response['start_offset'], $response['end_offset']);
    }

    /**
     * Upload by chunks - finish phase.
     *
     * @param string $endpoint
     * @param string $uploadSessionId
     * @param array  $metadata        the metadata associated with the file
     *
     * @return bool
     *
     * @throws FacebookSDKException
     */
    public function finish($endpoint, $uploadSessionId, $metadata = [])
    {
        $params = array_merge($metadata, [
            'upload_phase' => 'finish',
            'upload_session_id' => $uploadSessionId,
        ]);
        $response = $this->sendUploadRequest($endpoint, $params);

        return $response['success'];
    }

    /**
     * Helper to make a FacebookRequest and send it.
     *
     * @param string $endpoint the endpoint to POST to
     * @param array  $params   the params to send with the request
     *
     * @return array
     */
    private function sendUploadRequest($endpoint, $params = [])
    {
        $request = new FacebookRequest($this->app, $this->accessToken, 'POST', $endpoint, $params, null, $this->graphVersion);

        return $this->client->sendRequest($request)->getDecodedBody();
    }
}
