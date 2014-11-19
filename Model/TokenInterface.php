<?php

namespace Bazinga\OAuthServerBundle\Model;

use Symfony\Component\Security\Core\User\UserInterface;

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
     * Set token
     * @param string $token
     * @return self
     */
    public function setToken($token);

    /**
     * Returns the secret string.
     * @return string
     */
    public function getSecret();

    /**
     * Set secret
     * @param string $secret
     * @return self
     */
    public function setSecret($secret);

    /**
     * Returns the expiration time.
     * @return int
     */
    public function getExpiresAt();

    /**
     * Set expiresAt
     * @param int $expiresAt
     * @return self
     */
    public function setExpiresAt($expiresAt);

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
     * @return UserInterface
     */
    public function getUser();

    /**
     * Sets the user for this token.
     * @return self
     */
    public function setUser(UserInterface $user);

    /**
     * Returns the consumer for this token.
     * @return ConsumerInterface
     */
    public function getConsumer();

    /**
     * Set consumer
     * @param ConsumerInterface $consumer
     * @return self
     */
    public function setConsumer(ConsumerInterface $consumer);
}
