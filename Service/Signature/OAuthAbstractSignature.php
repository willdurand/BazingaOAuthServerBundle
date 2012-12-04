<?php

namespace Bazinga\OAuthServerBundle\Service\Signature;

/**
 * OAuthAbstractSignature class.
 *
 * @author William DURAND <william.durand1@gmail.com>
 */
abstract class OAuthAbstractSignature implements OAuthSignatureInterface
{
    /**
     * Returns an encoded string according to the RFC3986.
     *
     * @return string
     */
    public function urlencode($string)
    {
        return str_replace('%7E', '~', rawurlencode($string));
    }
}
