<?php

namespace Bazinga\OAuthBundle\Model\Provider;

/**
 * This interface represents an OAuth Consumer provider.
 *
 * @package     BazingaOAuthBundle
 * @subpackage  Provider
 * @author William DURAND <william.durand1@gmail.com>
 */
interface OAuthConsumerProviderInterface
{
    /**
     * @return Bazinga\OAuthBundle\Model\OAuthConsumerInterface
     */
    public function getConsumerByKey($consumerKey);
}
