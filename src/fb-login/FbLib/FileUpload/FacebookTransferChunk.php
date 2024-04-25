<?php





namespace Facebook\FileUpload;

/**
 * Class FacebookTransferChunk.
 */
class FacebookTransferChunk
{
    /**
     * @var FacebookFile the file to chunk during upload
     */
    private $file;

    /**
     * @var int the ID of the upload session
     */
    private $uploadSessionId;

    /**
     * @var int start byte position of the next file chunk
     */
    private $startOffset;

    /**
     * @var int end byte position of the next file chunk
     */
    private $endOffset;

    /**
     * @var int the ID of the video
     */
    private $videoId;

    /**
     * @param int $uploadSessionId
     * @param int $videoId
     * @param int $startOffset
     * @param int $endOffset
     */
    public function __construct(FacebookFile $file, $uploadSessionId, $videoId, $startOffset, $endOffset)
    {
        $this->file = $file;
        $this->uploadSessionId = $uploadSessionId;
        $this->videoId = $videoId;
        $this->startOffset = $startOffset;
        $this->endOffset = $endOffset;
    }

    /**
     * Return the file entity.
     *
     * @return FacebookFile
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * Return a FacebookFile entity with partial content.
     *
     * @return FacebookFile
     */
    public function getPartialFile()
    {
        $maxLength = $this->endOffset - $this->startOffset;

        return new FacebookFile($this->file->getFilePath(), $maxLength, $this->startOffset);
    }

    /**
     * Return upload session Id.
     *
     * @return int
     */
    public function getUploadSessionId()
    {
        return $this->uploadSessionId;
    }

    /**
     * Check whether is the last chunk.
     *
     * @return bool
     */
    public function isLastChunk()
    {
        return $this->startOffset === $this->endOffset;
    }

    /**
     * @return int
     */
    public function getStartOffset()
    {
        return $this->startOffset;
    }

    /**
     * Get uploaded video Id.
     *
     * @return int
     */
    public function getVideoId()
    {
        return $this->videoId;
    }
}
