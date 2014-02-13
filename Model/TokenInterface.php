<?php

namespace Bazinga\OAuthServerBundle\Model;

/**
 * This interface represents an OAuth token.
 *
 * @author William DURAND <william.durand1@gmail.com>
 */
interface TokenInterface
{
    /**
     * Returns the token string.
     * @return string
     */
    public function getToken();

    /**
     * Returns the secret string.
     * @return string
     */
    public function getSecret();

    /**
     * Returns the expiration time.
     * @return int
     */
    public function getExpiresAt();

    /**
     * Returns the expiration delay.
     * @return integer
     */
    public function getExpiresIn();

    /**
     * Returns whether the token is still valid or not.
     * @return boolean
     */
    public function hasExpired();

    /**
     * Returns the user for this token.
     * @return \Bazinga\OAuthServerBundle\Model\UserInterface
     */
    public function getUser();

    /**
     * Returns the consumer for this token.
     * @return \Bazinga\OAuthServerBundle\Model\ConsumerInterface
     */
    public function getConsumer();
}
