<?php

namespace Bazinga\OAuthServerBundle\Model\Provider;

use Bazinga\OAuthServerBundle\Model\ConsumerInterface;

/**
 * This interface represents an OAuth Nonce provider.
 *
 * @author William DURAND <william.durand1@gmail.com>
 */
interface NonceProviderInterface
{
    /**
     * @param $nonce
     * @param $timestamp
     * @param  \Bazinga\OAuthServerBundle\Model\ConsumerInterface $consumer
     * @return boolean
     */
    public function checkNonceAndTimestampUnicity($nonce, $timestamp, ConsumerInterface $consumer);

    /**
     * @param $nonce
     * @param $timestamp
     * @param  \Bazinga\OAuthServerBundle\Model\ConsumerInterface $consumer
     * @return boolean
     */
    public function registerNonceAndTimestamp($nonce, $timestamp, ConsumerInterface $consumer);
}
