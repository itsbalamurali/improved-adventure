<?php





namespace Facebook;

/**
 * Class GraphSessionInfo.
 *
 * @author Fosco Marotto <fjm@fb.com>
 * @author David Poll <depoll@fb.com>
 */
class GraphSessionInfo extends GraphObject
{
    /**
     * Returns the application id the token was issued for.
     *
     * @return null|string
     */
    public function getAppId()
    {
        return $this->getProperty('app_id');
    }

    /**
     * Returns the application name the token was issued for.
     *
     * @return null|string
     */
    public function getApplication()
    {
        return $this->getProperty('application');
    }

    /**
     * Returns the date & time that the token expires.
     *
     * @return null|\DateTime
     */
    public function getExpiresAt()
    {
        $stamp = $this->getProperty('expires_at');
        if ($stamp) {
            return (new \DateTime())->setTimestamp($stamp);
        }

        return null;
    }

    /**
     * Returns whether the token is valid.
     *
     * @return bool
     */
    public function isValid()
    {
        return $this->getProperty('is_valid');
    }

    /**
     * Returns the date & time the token was issued at.
     *
     * @return null|\DateTime
     */
    public function getIssuedAt()
    {
        $stamp = $this->getProperty('issued_at');
        if ($stamp) {
            return (new \DateTime())->setTimestamp($stamp);
        }

        return null;
    }

    /**
     * Returns the scope permissions associated with the token.
     *
     * @return array
     */
    public function getScopes()
    {
        return $this->getPropertyAsArray('scopes');
    }

    /**
     * Returns the login id of the user associated with the token.
     *
     * @return null|string
     */
    public function getId()
    {
        return $this->getProperty('user_id');
    }
}
