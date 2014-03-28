<?php

namespace Bazinga\OAuthServerBundle\Model;

/**
 * This interface represents an OAuth request token.
 *
 * @author William DURAND <william.durand1@gmail.com>
 */
interface RequestTokenInterface extends TokenInterface
{
    /**
     * Returns the verifier string.
     * @return string
     */
    public function getVerifier();

    /**
     * Sets the verifier string.
     * @param string $verifier
     * @return self
     */
    public function setVerifier($verifier);
}
