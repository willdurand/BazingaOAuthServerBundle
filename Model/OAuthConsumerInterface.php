<?php

namespace Bazinga\OAuthServerBundle\Model;

/**
 * This interface represents an OAuth Consumer.
 *
 * @package     BazingaOAuthServerBundle
 * @subpackage  Model
 * @author William DURAND <william.durand1@gmail.com>
 */
interface OAuthConsumerInterface
{
    /**
     * Returns the consumer name.
     * @return string The consumer name.
     */
    public function getName();
    /**
     * Returns the consumer key.
     * @return string The consumer key.
     */
    public function getConsumerKey();
    /**
     * Returns the consumer secret.
     * @return string The consumer secret.
     */
    public function getConsumerSecret();
    /**
     * Returns the callback.
     * @return string The callback.
     */
    public function getCallback();
}
