<?php

namespace Bazinga\OAuthServerBundle\Model\Provider;

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
    public function getClass();

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
}
