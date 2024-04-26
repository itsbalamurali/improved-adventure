<?php





namespace Facebook\GraphNodes;

/**
 * Class GraphGroup.
 */
class GraphGroup extends GraphNode
{
    /**
     * @var array maps object key names to GraphNode types
     */
    protected static $graphObjectMap = [
        'cover' => '\Facebook\GraphNodes\GraphCoverPhoto',
        'venue' => '\Facebook\GraphNodes\GraphLocation',
    ];

    /**
     * Returns the `id` (The Group ID) as string if present.
     *
     * @return null|string
     */
    public function getId()
    {
        return $this->getField('id');
    }

    /**
     * Returns the `cover` (The cover photo of the Group) as GraphCoverPhoto if present.
     *
     * @return null|GraphCoverPhoto
     */
    public function getCover()
    {
        return $this->getField('cover');
    }

    /**
     * Returns the `description` (A brief description of the Group) as string if present.
     *
     * @return null|string
     */
    public function getDescription()
    {
        return $this->getField('description');
    }

    /**
     * Returns the `email` (The email address to upload content to the Group. Only current members of the Group can use this) as string if present.
     *
     * @return null|string
     */
    public function getEmail()
    {
        return $this->getField('email');
    }

    /**
     * Returns the `icon` (The URL for the Group's icon) as string if present.
     *
     * @return null|string
     */
    public function getIcon()
    {
        return $this->getField('icon');
    }

    /**
     * Returns the `link` (The Group's website) as string if present.
     *
     * @return null|string
     */
    public function getLink()
    {
        return $this->getField('link');
    }

    /**
     * Returns the `name` (The name of the Group) as string if present.
     *
     * @return null|string
     */
    public function getName()
    {
        return $this->getField('name');
    }

    /**
     * Returns the `member_request_count` (Number of people asking to join the group.) as int if present.
     *
     * @return null|int
     */
    public function getMemberRequestCount()
    {
        return $this->getField('member_request_count');
    }

    /**
     * Returns the `owner` (The profile that created this Group) as GraphNode if present.
     *
     * @return null|GraphNode
     */
    public function getOwner()
    {
        return $this->getField('owner');
    }

    /**
     * Returns the `parent` (The parent Group of this Group, if it exists) as GraphNode if present.
     *
     * @return null|GraphNode
     */
    public function getParent()
    {
        return $this->getField('parent');
    }

    /**
     * Returns the `privacy` (The privacy setting of the Group) as string if present.
     *
     * @return null|string
     */
    public function getPrivacy()
    {
        return $this->getField('privacy');
    }

    /**
     * Returns the `updated_time` (The last time the Group was updated (this includes changes in the Group's properties and changes in posts and comments if user can see them)) as \DateTime if present.
     *
     * @return null|\DateTime
     */
    public function getUpdatedTime()
    {
        return $this->getField('updated_time');
    }

    /**
     * Returns the `venue` (The location for the Group) as GraphLocation if present.
     *
     * @return null|GraphLocation
     */
    public function getVenue()
    {
        return $this->getField('venue');
    }
}
