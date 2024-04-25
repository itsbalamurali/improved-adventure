<?php





namespace Facebook\GraphNodes;

/**
 * Class GraphSessionInfo.
 */
class GraphSessionInfo extends GraphNode
{
    /**
     * Returns the application id the token was issued for.
     *
     * @return null|string
     */
    public function getAppId()
    {
        return $this->getField('app_id');
    }

    /**
     * Returns the application name the token was issued for.
     *
     * @return null|string
     */
    public function getApplication()
    {
        return $this->getField('application');
    }

    /**
     * Returns the date & time that the token expires.
     *
     * @return null|\DateTime
     */
    public function getExpiresAt()
    {
        return $this->getField('expires_at');
    }

    /**
     * Returns whether the token is valid.
     *
     * @return bool
     */
    public function getIsValid()
    {
        return $this->getField('is_valid');
    }

    /**
     * Returns the date & time the token was issued at.
     *
     * @return null|\DateTime
     */
    public function getIssuedAt()
    {
        return $this->getField('issued_at');
    }

    /**
     * Returns the scope permissions associated with the token.
     *
     * @return array
     */
    public function getScopes()
    {
        return $this->getField('scopes');
    }

    /**
     * Returns the login id of the user associated with the token.
     *
     * @return null|string
     */
    public function getUserId()
    {
        return $this->getField('user_id');
    }
}
