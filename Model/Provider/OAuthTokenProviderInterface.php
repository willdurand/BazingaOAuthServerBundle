<?php

namespace Bazinga\OAuthBundle\Model\Provider;

use Bazinga\OAuthBundle\Model\OAuthAccessTokenInterface;
use Bazinga\OAuthBundle\Model\OAuthConsumerInterface;
use Bazinga\OAuthBundle\Model\OAuthRequestTokenInterface;
use Bazinga\OAuthBundle\Model\OAuthUserInterface;

/**
 * OAuthTokenProviderInterface interface.
 *
 * @package     BazingaOAuthBundle
 * @subpackage  Provider
 * @author William DURAND <william.durand1@gmail.com>
 */
interface OAuthTokenProviderInterface
{
    /**
     * Create a request token.
     *
     * @param \Bazinga\OAuthBundle\Model\OAuthConsumerInterface $consumer An OAuth consumer.
     * @return \Bazinga\OAuthBundle\Model\OAuthRequestTokenInterface
     */
    function createRequestToken(OAuthConsumerInterface $consumer);
    /**
     * Create an access token.
     *
     * @param \Bazinga\OAuthBundle\Model\OAuthConsumerInterface $consumer An OAuth consumer.
     * @param
     * @return \Bazinga\OAuthBundle\Model\OAuthAccessTokenInterface
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
