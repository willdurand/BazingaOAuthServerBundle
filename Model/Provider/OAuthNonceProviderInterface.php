<?php

namespace Bazinga\OAuthServerBundle\Model\Provider;

use Bazinga\OAuthServerBundle\Model\OAuthConsumerInterface;

/**
 * This interface represents an OAuth Nonce provider.
 *
 * @package     BazingaOAuthServerBundle
 * @subpackage  Provider
 * @author William DURAND <william.durand1@gmail.com>
 */
interface OAuthNonceProviderInterface
{
    /**
     * @param $nonce
     * @param $timestamp
     * @param  \Bazinga\OAuthServerBundle\Model\OAuthConsumerInterface $consumer
     * @return boolean
     */
    public function checkNonceAndTimestampUnicity($nonce, $timestamp, OAuthConsumerInterface $consumer);

    /**
     * @param $nonce
     * @param $timestamp
     * @param  \Bazinga\OAuthServerBundle\Model\OAuthConsumerInterface $consumer
     * @return boolean
     */
    public function registerNonceAndTimestamp($nonce, $timestamp, OAuthConsumerInterface $consumer);
}
