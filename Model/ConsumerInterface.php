<?php

namespace Bazinga\OAuthServerBundle\Model;

/**
 * This interface represents an OAuth Consumer.
 *
 * @author William DURAND <william.durand1@gmail.com>
 */
interface ConsumerInterface
{
    /**
     * Sets the consumer name.
     * @param string $name The consumer name.
     */
    public function setName($name);

    /**
     * Returns the consumer name.
     * @return string The consumer name.
     */
    public function getName();

    /**
     * Sets the consumer key.
     * @param string $consumerKey The consumer key.
     */
    public function setConsumerKey($consumerKey);

    /**
     * Returns the consumer key.
     * @return string The consumer key.
     */
    public function getConsumerKey();

    /**
     * Sets the consumer secret.
     * @param string $consumerSecret The consumer secret.
     */
    public function setConsumerSecret($consumerSecret);

    /**
     * Returns the consumer secret.
     * @return string The consumer secret.
     */
    public function getConsumerSecret();

    /**
     * Sets the callback.
     * @return string $callback The callback.
     */
    public function setCallback($callback);

    /**
     * Returns the callback.
     * @return string The callback.
     */
    public function getCallback();
}
