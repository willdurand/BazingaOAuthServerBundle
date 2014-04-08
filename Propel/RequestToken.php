<?php

namespace Bazinga\OAuthServerBundle\Propel;

use Bazinga\OAuthServerBundle\Model\RequestTokenInterface;

class RequestToken extends Token implements RequestTokenInterface
{
    public function __construct()
    {
        parent::__construct();

        $this->setType(TokenPeer::CLASSKEY_REQUEST_TOKEN);
    }

}
