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
     * @return boolean
     */
    function checkNonceAndTimestampUnicity($nonce, $timestamp, OAuthConsumerInterface $consumer);

    /**
     */
    function registerNonceAndTimestamp($nonce, $timestamp, OAuthConsumerInterface $consumer);
}
