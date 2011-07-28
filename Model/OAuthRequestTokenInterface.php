<?php

namespace Bazinga\OAuthBundle\Model;

use Bazinga\OAuthBundle\Model\OAuthTokenInterface;

/**
 * This interface represents an OAuth request token.
 *
 * @package     BazingaOAuthBundle
 * @subpackage  Model
 * @author William DURAND <william.durand1@gmail.com>
 */
interface OAuthRequestTokenInterface extends OAuthTokenInterface
{
    /**
     * Returns the verifier string.
     * @return string
     */
    function getVerifier();
}
