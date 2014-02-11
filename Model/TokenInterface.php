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
     * Sets the token string
     * @param string $token
     */
    public function setToken($token);

    /**
     * Returns the token string.
     * @return string
     */
    public function getToken();

    /**
     * Sets the secret string
     * @param string $secret
     */
    public function setSecret($secret);

    /**
     * Returns the secret string.
     * @return string
     */
    public function getSecret();

    /**
     * Sets the expiration time.
     * @param int $expiresAt
     */
    public function setExpiresAt($expiresAt);

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
     * Sets the user for this token.
     * @param \Bazinga\OAuthServerBundle\Model\UserInterface $user
     */
    public function setUser(UserInterface $user);

    /**
     * Returns the user for this token.
     * @return \Bazinga\OAuthServerBundle\Model\UserInterface
     */
    public function getUser();

    /**
     * Sets the consumer for this token.
     * @param \Bazinga\OAuthServerBundle\Model\OAuthConsumerInterface $consumer
     */
    public function setConsumer(ConsumerInterface $consumer);

    /**
     * Returns the consumer for this token.
     * @return \Bazinga\OAuthServerBundle\Model\ConsumerInterface
     */
    public function getConsumer();
}
