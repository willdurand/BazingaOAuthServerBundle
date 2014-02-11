<?php

namespace Bazinga\OAuthServerBundle\Model;

/**
 * @author Robin van der Vleuten <robinvdvleuten@gmail.com>
 */
class RequestToken extends Token implements RequestTokenInterface
{
    /**
     * @var string
     */
    protected $verifier;

    /**
     * {@inheritDoc}
     */
    public function setVerifier($verifier)
    {
        $this->verifier = $verifier;
    }

    /**
     * {@inheritDoc}
     */
    public function getVerifier()
    {
        return $this->verifier;
    }
}
