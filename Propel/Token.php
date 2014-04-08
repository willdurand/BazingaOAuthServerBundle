<?php

namespace Bazinga\OAuthServerBundle\Propel;

use Bazinga\OAuthServerBundle\Propel\om\BaseToken;

abstract class Token extends BaseToken
{
    /**
     * {@inheritDoc}
     */
    public function getExpiresIn()
    {
        if ($this->getExpiresAt()) {
            return $this->getExpiresAt() - time();
        }

        return PHP_INT_MAX;
    }

    /**
     * {@inheritDoc}
     */
    public function hasExpired()
    {
        if ($this->getExpiresAt()) {
            return time() > $this->getExpiresAt();
        }

        return false;
    }
}
