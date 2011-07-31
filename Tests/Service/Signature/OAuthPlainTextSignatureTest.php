<?php

namespace Bazinga\OAuthServerBundle\Tests\Service\Signature;

use Bazinga\OAuthServerBundle\Tests\TestCase;
use Bazinga\OAuthServerBundle\Service\Signature\OAuthPlainTextSignature;

/**
 * @author William DURAND <william.durand1@gmail.com>
 */
class OAuthSignatureMethodPlaintextTest extends TestCase
{
	private $method;

    public function setUp()
    {
		$this->method = new OAuthPlainTextSignature();
	}

    public function testGetName()
    {
		$this->assertEquals('PLAINTEXT', $this->method->getName());
	}

    public function testSign()
    {
		// Tests based on from http://wiki.oauth.net/TestCases section 9.2 ("HMAC-SHA1")
        $baseString     = 'unused';
        $consumerSecret = 'cs';

		$tokenSecret    = NULL;
		$this->assertEquals('cs%26', $this->method->sign($baseString, $consumerSecret, $tokenSecret));

		$tokenSecret    = 'ts';
		$this->assertEquals('cs%26ts', $this->method->sign($baseString, $consumerSecret, $tokenSecret));

		$consumerSecret = 'kd94hf93k423kf44';
		$tokenSecret    = 'pfkkdhi9sl3r4s00';
		$this->assertEquals('kd94hf93k423kf44%26pfkkdhi9sl3r4s00', $this->method->sign($baseString, $consumerSecret, $tokenSecret));

		// Tests taken from Chapter 9.4.1 ("Generating Signature") from the spec
		$consumerSecret = 'djr9rjt0jd78jf88';
		$tokenSecret    = 'jjd999tj88uiths3';
		$this->assertEquals('djr9rjt0jd78jf88%26jjd999tj88uiths3', $this->method->sign($baseString, $consumerSecret, $tokenSecret));

		$consumerSecret = 'djr9rjt0jd78jf88';
		$tokenSecret    = 'jjd99$tj88uiths3';
		$this->assertEquals('djr9rjt0jd78jf88%26jjd99%24tj88uiths3', $this->method->sign($baseString, $consumerSecret, $tokenSecret));
	}
}
