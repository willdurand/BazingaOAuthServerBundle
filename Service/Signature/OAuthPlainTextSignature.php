<?php

namespace Bazinga\OAuthServerBundle\Service\Signature;

/**
 * Plain text signature.
 *
 * @package     BazingaOAuthServerBundle
 * @subpackage  Signature
 * @author William DURAND <william.durand1@gmail.com>
 */
class OAuthPlainTextSignature extends OAuthAbstractSignature
{
    /**
     * {@inheritdoc}
     */
    public function sign($baseString, $consumerSecret, $tokenSecret = '')
    {
        return $this->urlencode($consumerSecret) . $this->urlencode('&') . $this->urlencode($tokenSecret);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'PLAINTEXT';
    }
}
