<?php

namespace Bazinga\OAuthServerBundle\Model;

use Bazinga\OAuthServerBundle\Model\OAuthTokenInterface;

/**
 * This interface represents an OAuth request token.
 *
 * @author William DURAND <william.durand1@gmail.com>
 */
interface OAuthRequestTokenInterface extends OAuthTokenInterface
{
    /**
     * Returns the verifier string.
     * @return string
     */
    public function getVerifier();
}
