<?php

namespace Bazinga\OAuthServerBundle\Tests\Service;

use Bazinga\OAuthServerBundle\Tests\TestCase;

use Bazinga\OAuthServerBundle\Service\OAuthAbstractServerService;
use Bazinga\OAuthServerBundle\Model\ConsumerInterface;
use Bazinga\OAuthServerBundle\Model\TokenInterface;

/**
 * @author William DURAND <william.durand1@gmail.com>
 */
class OAuthAbstractServerServiceTest extends TestCase
{

    protected $service;

    public function setUp()
    {
        $this->service = new ConcreteOauthServerService();
    }

    public function getTokenMock($tokenString, $secretString, $expiresIn)
    {
        $token = $this
            ->getMock('Bazinga\OAuthServerBundle\Model\TokenInterface');

        $token
            ->expects($this->once())
            ->method('getToken')
            ->will($this->returnValue($tokenString))
            ;

        $token
            ->expects($this->once())
            ->method('getSecret')
            ->will($this->returnValue($secretString))
            ;

        $token
            ->expects($this->atLeastOnce())
            ->method('getExpiresIn')
            ->will($this->returnValue($expiresIn))
            ;

        return $token;
    }

    public function testCheckTimestamp()
    {
        $result = $this->service->checkTimestamp(time());
        $this->assertTrue($result, 'Timestamp is correct');

        $result = $this->service->checkTimestamp(0);
        $this->assertFalse($result, 'Timestamp is incorrect');

        $result = $this->service->checkTimestamp(null);
        $this->assertFalse($result, 'Timestamp is incorrect');

        $result = $this->service->checkTimestamp(1203242354324344);
        $this->assertFalse($result, 'Timestamp is incorrect');

        $result = $this->service->checkTimestamp(-12232145432);
        $this->assertFalse($result, 'Timestamp is incorrect');

        $result = $this->service->checkTimestamp(-1 * time());
        $this->assertFalse($result, 'Timestamp is incorrect');
    }

    public function testCheckVersion()
    {
        $result = $this->service->checkVersion('1.0');
        $this->assertTrue($result, 'Version is correct');

        $result = $this->service->checkVersion('');
        $this->assertFalse($result, 'Version is incorrect');

        $result = $this->service->checkVersion(null);
        $this->assertFalse($result, 'Version is incorrect');

        $result = $this->service->checkVersion('2.0');
        $this->assertFalse($result, 'Version is incorrect');

        $result = $this->service->checkVersion(1.0);
        $this->assertFalse($result, 'Version is incorrect');
    }

    public function testNormalizeRequestParameters()
    {
        $array  = array(
            'a' => 'bar',
            'b' => 'too',
            'c' => 'lait'
        );

        $result = $this->service->normalizeRequestParameters($array);
        $this->assertEquals('a=bar&b=too&c=lait', $result, 'Test basic normalization');

        $array  = array(
            'z' => 'yop',
            'a' => 'too',
            't' => 'bar',
            'x' => 'foo',
        );

        $result = $this->service->normalizeRequestParameters($array);
        $this->assertEquals('a=too&t=bar&x=foo&z=yop', $result, 'Array has to be sorted');

        $array  = array(
            'z' => array('yop', 'too', 'foo'),
            'a' => 'bar',
        );

        $result = $this->service->normalizeRequestParameters($array);
        $this->assertEquals('a=bar&z=foo&z=too&z=yop', $result, 'Array has to be sorted and values too');
    }

    public function testNormalizeRequestParametersWithEmptyArray()
    {
        $result = $this->service->normalizeRequestParameters(array());
        $this->assertEquals('', $result, 'Empty array means empty output string');
    }

    public function testNormalizeRequestParametersWithNullInput()
    {
        $result = $this->service->normalizeRequestParameters(null);
        $this->assertEquals('', $result, 'Null input means empty output string');
    }

    public function testNormalizeRequestParametersEncoded()
    {
        $array  = array(
                'a' => 'bar',
                'b' => 'email:test',
                'c' => 'example@example.com'
        );

        $result = $this->service->normalizeRequestParameters($array);
        $this->assertEquals('a=bar&b=email%3Atest&c=example%40example.com', $result, 'Test basic encoded params normalization');
    }

    public function testSendToken()
    {
        $token  = $this->getTokenMock('my_token', 'MySup3rSecr3t', null);
        $result = $this->service->sendToken($token);

        $this->assertEquals('oauth_token=my_token&oauth_token_secret=MySup3rSecr3t&oauth_expires_in=3600', $result, '');

        $token  = $this->getTokenMock('my_token', 'MySup3rSecr3t', 200);
        $result = $this->service->sendToken($token);

        $this->assertEquals('oauth_token=my_token&oauth_token_secret=MySup3rSecr3t&oauth_expires_in=200', $result, '');
    }
}

class ConcreteOauthServerService extends OAuthAbstractServerService
{
    public function __construct()
    {
    }

    public function checkTimestamp($oauthTimestamp)
    {
        return parent::checkTimestamp($oauthTimestamp);
    }

    public function checkVersion($oauthVersion)
    {
        return parent::checkVersion($oauthVersion);
    }

    public function normalizeRequestParameters($requestParameters)
    {
        return parent::normalizeRequestParameters($requestParameters);
    }

    public function approveSignature(ConsumerInterface $consumer, TokenInterface $token = null, $requestParameters, $requestMethod, $requestUrl)
    {
        return parent::approveSignature($consumer, $token, $requestParameters, $requestMethod, $requestUrl);
    }

    public function sendToken(TokenInterface $token, $lifetime = 3600, array $extras = array())
    {
        return parent::sendToken($token, $lifetime, $extras);
    }

    /**
     * Just implements interface
     */
    public function requestToken($requestParameters, $requestMethod, $requestUrl)
    {
    }

    /**
     * Just implements interface
     */
    public function authorize($oauthToken, $oauthCallback = null)
    {
    }

    /**
     * Just implements interface
     */
    public function accessToken($requestParameters, $requestMethod, $requestUrl)
    {
    }

    /**
     * Just implements interface
     */
    public function validateRequest($requestParameters, $requestMethod, $requestUrl)
    {
    }
}
