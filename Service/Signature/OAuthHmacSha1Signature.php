<?php

namespace Bazinga\OAuthServerBundle\Service\Signature;

/**
 * HMAC-SHA1 signature.
 *
 * @author William DURAND <william.durand1@gmail.com>
 */
class OAuthHmacSha1Signature extends OAuthAbstractSignature
{
    /**
     * {@inheritdoc}
     */
    public function sign($baseString, $consumerSecret, $tokenSecret = '')
    {
        $key = $this->urlencode($consumerSecret) . '&' . $this->urlencode($tokenSecret);

        if (function_exists('hash_hmac')) {
            $signature = (hash_hmac('sha1', $baseString, $key, true));
        } else {
            $signature = $this->hashHmacSha1($baseString, $key);
        }

        return base64_encode($signature);
    }

    /**
     * @see http://code.google.com/p/oauth-php/source/browse/trunk/library/signature_method/OAuthSignatureMethod_HMAC_SHA1.php
     */
    protected function hashHmacSha1($baseString, $key)
    {
        $blocksize  = 64;
        $hashfunc   = 'sha1';

        if (strlen($key) > $blocksize) {
            $key = pack('H*', $hashfunc($key));
        }
        $key  = str_pad($key, $blocksize, chr(0x00));
        $ipad = str_repeat(chr(0x36),$blocksize);
        $opad = str_repeat(chr(0x5c),$blocksize);
        $hmac = pack(
            'H*', $hashfunc(
                ($key^$opad) . pack(
                    'H*', $hashfunc(
                        ($key^$ipad) . $baseString
                    )
                )
            )
        );

        return $hmac;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'HMAC-SHA1';
    }
}
