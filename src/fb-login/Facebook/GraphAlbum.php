<?php





namespace Facebook;

/**
 * Class GraphAlbum.
 *
 * @author Daniele Grosso <daniele.grosso@gmail.com>
 */
class GraphAlbum extends GraphObject
{
    /**
     * Returns the ID for the album.
     *
     * @return null|string
     */
    public function getId()
    {
        return $this->getProperty('id');
    }

    /**
     * Returns whether the viewer can upload photos to this album.
     *
     * @return null|bool
     */
    public function canUpload()
    {
        return $this->getProperty('can_upload');
    }

    /**
     * Returns the number of photos in this album.
     *
     * @return null|int
     */
    public function getCount()
    {
        return $this->getProperty('count');
    }

    /**
     * Returns the ID of the album's cover photo.
     *
     * @return null|string
     */
    public function getCoverPhoto()
    {
        return $this->getProperty('cover_photo');
    }

    /**
     * Returns the time the album was initially created.
     *
     * @return null|\DateTime
     */
    public function getCreatedTime()
    {
        $value = $this->getProperty('created_time');
        if ($value) {
            return new \DateTime($value);
        }

        return null;
    }

    /**
     * Returns the time the album was updated.
     *
     * @return null|\DateTime
     */
    public function getUpdatedTime()
    {
        $value = $this->getProperty('updated_time');
        if ($value) {
            return new \DateTime($value);
        }

        return null;
    }

    /**
     * Returns the description of the album.
     *
     * @return null|string
     */
    public function getDescription()
    {
        return $this->getProperty('description');
    }

    /**
     * Returns profile that created the album.
     *
     * @return null|GraphUser
     */
    public function getFrom()
    {
        return $this->getProperty('from', GraphUser::className());
    }

    /**
     * Returns a link to this album on Facebook.
     *
     * @return null|string
     */
    public function getLink()
    {
        return $this->getProperty('link');
    }

    /**
     * Returns the textual location of the album.
     *
     * @return null|string
     */
    public function getLocation()
    {
        return $this->getProperty('location');
    }

    /**
     * Returns the title of the album.
     *
     * @return null|string
     */
    public function getName()
    {
        return $this->getProperty('name');
    }

    /**
     * Returns the privacy settings for the album.
     *
     * @return null|string
     */
    public function getPrivacy()
    {
        return $this->getProperty('privacy');
    }

    /**
     * Returns the type of the album. enum{profile, mobile, wall, normal, album}.
     *
     * @return null|string
     */
    public function getType()
    {
        return $this->getProperty('type');
    }

    // TODO: public function getPlace() that should return GraphPage
}
