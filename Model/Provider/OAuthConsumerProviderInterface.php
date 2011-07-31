<?php

namespace Bazinga\OAuthServerBundle\Model\Provider;

/**
 * This interface represents an OAuth Consumer provider.
 *
 * @package     BazingaOAuthServerBundle
 * @subpackage  Provider
 * @author William DURAND <william.durand1@gmail.com>
 */
interface OAuthConsumerProviderInterface
{
    /**
     * @return Bazinga\OAuthServerBundle\Model\OAuthConsumerInterface
     */
    public function getConsumerByKey($consumerKey);
}
