<?php

namespace Bazinga\OAuthServerBundle\Model\Provider;

use Bazinga\OAuthServerBundle\Model\OAuthAccessTokenInterface;
use Bazinga\OAuthServerBundle\Model\OAuthConsumerInterface;
use Bazinga\OAuthServerBundle\Model\OAuthRequestTokenInterface;
use Bazinga\OAuthServerBundle\Model\OAuthUserInterface;

/**
 * OAuthTokenProviderInterface interface.
 *
 * @package     BazingaOAuthServerBundle
 * @subpackage  Provider
 * @author William DURAND <william.durand1@gmail.com>
 */
interface OAuthTokenProviderInterface
{
    /**
     * Create a request token.
     *
     * @param \Bazinga\OAuthServerBundle\Model\OAuthConsumerInterface $consumer An OAuth consumer.
     * @return \Bazinga\OAuthServerBundle\Model\OAuthRequestTokenInterface
     */
    function createRequestToken(OAuthConsumerInterface $consumer);
    /**
     * Create an access token.
     *
     * @param \Bazinga\OAuthServerBundle\Model\OAuthConsumerInterface $consumer An OAuth consumer.
     * @param
     * @return \Bazinga\OAuthServerBundle\Model\OAuthAccessTokenInterface
     */
    function createAccessToken(OAuthConsumerInterface $consumer, OAuthUserInterface $user);
    /**
     *
     */
    function loadRequestTokenByToken($oauth_token);
    /**
     *
     */
    function loadAccessTokenByToken($oauth_token);
    /**
     *
     */
    function setUserForRequestToken(OAuthRequestTokenInterface $token, OAuthUserInterface $user);
    /**
     *
     */
    function deleteRequestToken(OAuthRequestTokenInterface $requestToken);
    /**
     *
     */
    function deleteAccessToken(OAuthAccessTokenInterface $accessToken);
}
