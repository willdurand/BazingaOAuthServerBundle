<?php

namespace Bazinga\OAuthServerBundle\Model;

/**
 * This interface represents an OAuth token.
 *
 * @author William DURAND <william.durand1@gmail.com>
 */
interface OAuthTokenInterface
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
     * @return \Bazinga\OAuthServerBundle\Model\OAuthUserInterface
     */
    public function getUser();

    /**
     * Returns the consumer for this token.
     * @return \Bazinga\OAuthServerBundle\Model\OAuthConsumerInterface
     */
    public function getConsumer();
}
