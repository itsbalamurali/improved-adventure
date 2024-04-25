<?php





namespace Facebook\GraphNodes;

/**
 * Class GraphAlbum.
 */
class GraphAlbum extends GraphNode
{
    /**
     * @var array maps object key names to Graph object types
     */
    protected static $graphObjectMap = [
        'from' => '\Facebook\GraphNodes\GraphUser',
        'place' => '\Facebook\GraphNodes\GraphPage',
    ];

    /**
     * Returns the ID for the album.
     *
     * @return null|string
     */
    public function getId()
    {
        return $this->getField('id');
    }

    /**
     * Returns whether the viewer can upload photos to this album.
     *
     * @return null|bool
     */
    public function getCanUpload()
    {
        return $this->getField('can_upload');
    }

    /**
     * Returns the number of photos in this album.
     *
     * @return null|int
     */
    public function getCount()
    {
        return $this->getField('count');
    }

    /**
     * Returns the ID of the album's cover photo.
     *
     * @return null|string
     */
    public function getCoverPhoto()
    {
        return $this->getField('cover_photo');
    }

    /**
     * Returns the time the album was initially created.
     *
     * @return null|\DateTime
     */
    public function getCreatedTime()
    {
        return $this->getField('created_time');
    }

    /**
     * Returns the time the album was updated.
     *
     * @return null|\DateTime
     */
    public function getUpdatedTime()
    {
        return $this->getField('updated_time');
    }

    /**
     * Returns the description of the album.
     *
     * @return null|string
     */
    public function getDescription()
    {
        return $this->getField('description');
    }

    /**
     * Returns profile that created the album.
     *
     * @return null|GraphUser
     */
    public function getFrom()
    {
        return $this->getField('from');
    }

    /**
     * Returns profile that created the album.
     *
     * @return null|GraphPage
     */
    public function getPlace()
    {
        return $this->getField('place');
    }

    /**
     * Returns a link to this album on Facebook.
     *
     * @return null|string
     */
    public function getLink()
    {
        return $this->getField('link');
    }

    /**
     * Returns the textual location of the album.
     *
     * @return null|string
     */
    public function getLocation()
    {
        return $this->getField('location');
    }

    /**
     * Returns the title of the album.
     *
     * @return null|string
     */
    public function getName()
    {
        return $this->getField('name');
    }

    /**
     * Returns the privacy settings for the album.
     *
     * @return null|string
     */
    public function getPrivacy()
    {
        return $this->getField('privacy');
    }

    /**
     * Returns the type of the album.
     *
     * enum{ profile, mobile, wall, normal, album }
     *
     * @return null|string
     */
    public function getType()
    {
        return $this->getField('type');
    }
}
