<?php





namespace Facebook\GraphNodes;

/**
 * Birthday object to handle various Graph return formats.
 */
class Birthday extends \DateTime
{
    /**
     * @var bool
     */
    private $hasDate = false;

    /**
     * @var bool
     */
    private $hasYear = false;

    /**
     * Parses Graph birthday format to set indication flags, possible values:
     *
     *  MM/DD/YYYY
     *  MM/DD
     *  YYYY
     *
     * @see https://developers.facebook.com/docs/graph-api/reference/user
     *
     * @param string $date
     */
    public function __construct($date)
    {
        $parts = explode('/', $date);

        $this->hasYear = 3 === \count($parts) || 1 === \count($parts);
        $this->hasDate = 3 === \count($parts) || 2 === \count($parts);

        parent::__construct($date);
    }

    /**
     * Returns whether date object contains birth day and month.
     *
     * @return bool
     */
    public function hasDate()
    {
        return $this->hasDate;
    }

    /**
     * Returns whether date object contains birth year.
     *
     * @return bool
     */
    public function hasYear()
    {
        return $this->hasYear;
    }
}
