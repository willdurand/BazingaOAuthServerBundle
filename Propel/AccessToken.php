<?php

namespace Bazinga\OAuthServerBundle\Propel;

use Bazinga\OAuthServerBundle\Model\AccessTokenInterface;

class AccessToken extends Token implements AccessTokenInterface
{
    public function __construct()
    {
        parent::__construct();

        $this->setType(TokenPeer::CLASSKEY_ACCESS_TOKEN);
    }
}
