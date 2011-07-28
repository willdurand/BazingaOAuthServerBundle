<?php

namespace Bazinga\OAuthBundle\Service\Signature;

/**
 * RSA-SHA1 signature.
 *
 * @package     BazingaOAuthBundle
 * @subpackage  Signature
 * @author William DURAND <william.durand1@gmail.com>
 */
class OAuthRsaSha1Signature extends OAuthAbstractSignature
{
    /**
     * {@inheritdoc}
     */
    public function sign($baseString, $consumerSecret, $tokenSecret = '')
    {
        throw new \Exception('Not yet implemented.');
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'RSA-SHA1';
    }
}
