<?php

namespace Bazinga\OAuthServerBundle\Tests\Service\Signature;

use Bazinga\OAuthServerBundle\Tests\TestCase;
use Bazinga\OAuthServerBundle\Service\Signature\OAuthHmacSha1Signature;

/**
 * @author William DURAND <william.durand1@gmail.com>
 */
class OAuthHmacSha1SignatureTest extends TestCase
{
    private $method;

    public function setUp()
    {
        $this->method = new OAuthHmacSha1Signature();
    }

    public function testGetName()
    {
        $this->assertEquals('HMAC-SHA1', $this->method->getName());
    }

    public function testSign()
    {
        // Tests taken from http://wiki.oauth.net/TestCases section 9.2 ("HMAC-SHA1")
        $baseString     = 'bs';
        $consumerSecret = 'cs';

        $tokenSecret    = NULL;
        $this->assertEquals(
            'egQqG5AJep5sJ7anhXju1unge2I%3D',
            $this->method->sign($baseString, $consumerSecret, $tokenSecret),
            'token secret is null'
        );

        $tokenSecret    = 'ts';
        $this->assertEquals(
            'VZVjXceV7JgPq%2FdOTnNmEfO0Fv8%3D',
            $this->method->sign($baseString, $consumerSecret, $tokenSecret),
            'token secret is not null'
        );
    }
}
