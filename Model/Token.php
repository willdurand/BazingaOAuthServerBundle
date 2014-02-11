<?php

namespace Bazinga\OAuthServerBundle\Model;

/**
 * @author Robin van der Vleuten <robinvdvleuten@gmail.com>
 */
class Token implements TokenInterface
{
    /**
     * @var string
     */
    protected $token;

    /**
     * @var string
     */
    protected $secret;

    /**
     * @var int
     */
    protected $expiresAt;

    /**
     * @var \Bazinga\OAuthServerBundle\Model\OAuthUserInterface
     */
    protected $user;

    /**
     * @var string
     */
    protected $consumer;

    /**
     * {@inheritDoc}
     */
    public function setToken($token)
    {
        $this->token = $token;
    }

    /**
     * {@inheritDoc}
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * {@inheritDoc}
     */
    public function setSecret($secret)
    {
        $this->secret = $secret;
    }

    /**
     * {@inheritDoc}
     */
    public function getSecret()
    {
        return $this->secret;
    }

    /**
     * {@inheritDoc}
     */
    public function setExpiresAt($expiresAt)
    {
        $this->expiresAt = $expiresAt;
    }

    /**
     * {@inheritDoc}
     */
    public function getExpiresAt()
    {
        return $this->expiresAt;
    }

    /**
     * {@inheritDoc}
     */
    public function getExpiresIn()
    {
        if ($this->expiresAt) {
            return $this->expiresAt - time();
        }

        return PHP_INT_MAX;
    }

    /**
     * {@inheritDoc}
     */
    public function hasExpired()
    {
        if ($this->expiresAt) {
            return time() > $this->expiresAt;
        }

        return false;
    }

    /**
     * {@inheritDoc}
     */
    public function setUser(UserInterface $user)
    {
        $this->user = $user;
    }

    /**
     * {@inheritDoc}
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * {@inheritDoc}
     */
    public function setConsumer(ConsumerInterface $consumer)
    {
        $this->consumer = $consumer;
    }

    /**
     * {@inheritDoc}
     */
    public function getConsumer()
    {
        return $this->consumer;
    }
}
