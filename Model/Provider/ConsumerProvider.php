<?php

namespace Bazinga\OAuthServerBundle\Model\Provider;

/**
 * @author Robin van der Vleuten <robinvdvleuten@gmail.com>
 */
abstract class ConsumerProvider implements ConsumerProviderInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConsumerByKey($consumerKey)
    {
        return $this->getConsumerBy(array('consumerKey' => $consumerKey));
    }
}
