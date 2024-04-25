<?php





namespace Facebook\GraphNodes;

/**
 * Class GraphAchievement.
 */
class GraphAchievement extends GraphNode
{
    /**
     * @var array maps object key names to Graph object types
     */
    protected static $graphObjectMap = [
        'from' => '\Facebook\GraphNodes\GraphUser',
        'application' => '\Facebook\GraphNodes\GraphApplication',
    ];

    /**
     * Returns the ID for the achievement.
     *
     * @return null|string
     */
    public function getId()
    {
        return $this->getField('id');
    }

    /**
     * Returns the user who achieved this.
     *
     * @return null|GraphUser
     */
    public function getFrom()
    {
        return $this->getField('from');
    }

    /**
     * Returns the time at which this was achieved.
     *
     * @return null|\DateTime
     */
    public function getPublishTime()
    {
        return $this->getField('publish_time');
    }

    /**
     * Returns the app in which the user achieved this.
     *
     * @return null|GraphApplication
     */
    public function getApplication()
    {
        return $this->getField('application');
    }

    /**
     * Returns information about the achievement type this instance is connected with.
     *
     * @return null|array
     */
    public function getData()
    {
        return $this->getField('data');
    }

    /**
     * Returns the type of achievement.
     *
     * @see https://developers.facebook.com/docs/graph-api/reference/achievement
     *
     * @return string
     */
    public function getType()
    {
        return 'game.achievement';
    }

    /**
     * Indicates whether gaining the achievement published a feed story for the user.
     *
     * @return null|bool
     */
    public function isNoFeedStory()
    {
        return $this->getField('no_feed_story');
    }
}
