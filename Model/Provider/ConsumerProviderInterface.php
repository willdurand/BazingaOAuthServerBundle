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
     * @param $consumerKey
     * @return \Bazinga\OAuthServerBundle\Model\ConsumerInterface
     */
    public function getConsumerByKey($consumerKey);
}
