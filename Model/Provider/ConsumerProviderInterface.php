<?php

namespace Bazinga\OAuthServerBundle\Model\Provider;
use Bazinga\OAuthServerBundle\Model\ConsumerInterface;

/**
 * This interface represents an OAuth Consumer provider.
 *
 * @author William DURAND <william.durand1@gmail.com>
 */
interface ConsumerProviderInterface
{
    /**
     * Returns the consumer's fully qualified class name.
     *
     * @return string
     */
    public function getConsumerClass();

    /**
     * Create a consumer.
     *
     * @param string      $name
     * @param string|null $callback
     * @return \Bazinga\OAuthServerBundle\Model\ConsumerInterface
     */
    public function createConsumer($name, $callback = null);

    /**
     * @param array $criteria
     * @return \Bazinga\OAuthServerBundle\Model\ConsumerInterface
     */
    public function getConsumerBy(array $criteria);

    /**
     * @param $consumerKey
     * @return \Bazinga\OAuthServerBundle\Model\ConsumerInterface
     */
    public function getConsumerByKey($consumerKey);

    /**
     * Deletes a consumer.
     *
     * @param ConsumerInterface $consumer
     * @return void
     */
    public function deleteConsumer(ConsumerInterface $consumer);

    /**
     * Updates a consumer.
     *
     * @param ConsumerInterface $consumer
     * @return void
     */
    public function updateConsumer(ConsumerInterface $consumer);
}
