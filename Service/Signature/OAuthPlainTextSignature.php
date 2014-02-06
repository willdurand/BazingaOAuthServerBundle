<?php

namespace Bazinga\OAuthServerBundle\Service\Signature;

/**
 * Plain text signature.
 *
 * @author William DURAND <william.durand1@gmail.com>
 */
class OAuthPlainTextSignature extends OAuthAbstractSignature
{
    /**
     * {@inheritdoc}
     */
    public function sign($baseString, $consumerSecret, $tokenSecret = '')
    {
        return base64_encode($this->urlencode($consumerSecret) . '&' . $this->urlencode($tokenSecret));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'PLAINTEXT';
    }
}
