<?php

namespace Bazinga\OAuthServerBundle\Model\Provider;

use Bazinga\OAuthServerBundle\Model\AccessTokenInterface;
use Bazinga\OAuthServerBundle\Model\ConsumerInterface;
use Bazinga\OAuthServerBundle\Model\RequestTokenInterface;
use Bazinga\OAuthServerBundle\Model\TokenInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * OAuthTokenProviderInterface interface.
 *
 * @author William DURAND <william.durand1@gmail.com>
 */
interface TokenProviderInterface
{
    /**
     * Returns the request token's fully qualified class name.
     *
     * @return string
     */
    public function getRequestTokenClass();

    /**
     * Returns the access token's fully qualified class name.
     *
     * @return string
     */
    public function getAccessTokenClass();

    /**
     * Create a request token.
     *
     * @param  \Bazinga\OAuthServerBundle\Model\ConsumerInterface     $consumer An OAuth consumer.
     * @return \Bazinga\OAuthServerBundle\Model\RequestTokenInterface
     */
    public function createRequestToken(ConsumerInterface $consumer);

    /**
     * Create an access token.
     *
     * @param  \Bazinga\OAuthServerBundle\Model\ConsumerInterface    $consumer An OAuth consumer.
     * @param  \Bazinga\OAuthServerBundle\Model\UserInterface        $user
     * @return \Bazinga\OAuthServerBundle\Model\AccessTokenInterface
     */
    public function createAccessToken(ConsumerInterface $consumer, UserInterface $user);

    /**
     * @param array $criteria
     * @return \Bazinga\OAuthServerBundle\Model\RequestTokenInterface
     */
    public function loadRequestTokenBy(array $criteria);

    /**
     * @param $oauth_token
     * @return mixed
     */
    public function loadRequestTokenByToken($oauth_token);

    /**
     * @return \Traversable
     */
    public function loadRequestTokens();

    /**
     * @param array $criteria
     * @return \Bazinga\OAuthServerBundle\Model\AccessTokenInterface
     */
    public function loadAccessTokenBy(array $criteria);

    /**
     * @param $oauth_token
     * @return mixed
     */
    public function loadAccessTokenByToken($oauth_token);

    /**
     * @return \Traversable
     */
    public function loadAccessTokens();

    /**
     * @param  \Bazinga\OAuthServerBundle\Model\RequestTokenInterface $token
     * @param  \Bazinga\OAuthServerBundle\Model\UserInterface         $user
     * @return mixed
     */
    public function setUserForRequestToken(RequestTokenInterface $requestToken, UserInterface $user);

    /**
     * @param  \Bazinga\OAuthServerBundle\Model\RequestTokenInterface $requestToken
     * @return mixed
     */
    public function deleteRequestToken(RequestTokenInterface $requestToken);

    /**
     * @param  \Bazinga\OAuthServerBundle\Model\AccessTokenInterface $accessToken
     * @return mixed
     */
    public function deleteAccessToken(AccessTokenInterface $accessToken);

    /**
     * @return int The number of tokens deleted.
     */
    public function deleteExpired();

    /**
     * Deletes a token.
     *
     * @param TokenInterface $token
     * @return void
     */
    public function deleteToken(TokenInterface $token);

    /**
     * Updates a token.
     *
     * @param TokenInterface $token
     * @return void
     */
    public function updateToken(TokenInterface $token);
}
