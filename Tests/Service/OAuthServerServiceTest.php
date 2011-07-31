<?php

namespace Bazinga\OAuthServerBundle\Tests\Service;

use Bazinga\OAuthServerBundle\Tests\TestCase;

use Bazinga\OAuthServerBundle\Service\OAuthServerService;

/**
 * @package     BazingaOAuthServerBundle
 * @subpackage  Service
 * @author William DURAND <william.durand1@gmail.com>
 */
class OAuthServerServiceTest extends TestCase
{
    protected $service;

    public function setUp()
    {
        $this->service = new OAuthServerServiceMock();
    }

    public function testCheckConsumer()
    {
        try {
            $this->service->checkConsumer(null);
            $this->fail('Should throw an exception');
        } catch (\Exception $e) {
            $this->assertTrue(true, 'Exception catched');
            $this->assertEquals(401, $e->getStatusCode(), 'Status code should be 401');
            $this->assertEquals(OAuthServerService::ERROR_CONSUMER_KEY_UNKNOWN, $e->getMessage(), 'Check message');
        }

        try {
            $this->service->checkConsumer('');
            $this->fail('Should throw an exception');
        } catch (\Exception $e) {
            $this->assertTrue(true, 'Exception catched');
            $this->assertEquals(401, $e->getStatusCode(), 'Status code should be 401');
            $this->assertEquals(OAuthServerService::ERROR_CONSUMER_KEY_UNKNOWN, $e->getMessage(), 'Check message');
        }

        $consumer = $this->getMock('\Bazinga\OAuthServerBundle\Model\OAuthConsumerInterface');

        try {
            $this->assertTrue(true, $this->service->checkConsumer($consumer));
        } catch (\Exception $e) {
            $this->fail('Unexpected exception catched.');
        }
    }

    public function testCheckRequirements()
    {
        try {
            $this->service->checkRequirements(null);
            $this->fail('Should throw an exception');
        } catch (\Exception $e) {
            $this->assertTrue(true, 'Exception catched');
            $this->assertEquals(400, $e->getStatusCode(), 'Status code should be 400');
            $this->assertEquals(OAuthServerService::ERROR_PARAMETER_ABSENT, $e->getMessage(), 'Check message');
        }

        $params   = array(
            'foo' => 'bar'
        );
        $required = array('foo', 'baz');

        try {
            $this->service->checkRequirements($params, $required);
            $this->fail('Should throw an exception');
        } catch (\Exception $e) {
            $this->assertTrue(true, 'Exception catched');
            $this->assertEquals(400, $e->getStatusCode(), 'Status code should be 400');
            $this->assertEquals(OAuthServerService::ERROR_PARAMETER_ABSENT, $e->getMessage(), 'Check message');
        }

        $params   = array(
            'foo'             => 'bar',
            'oauth_timestamp' => time()
        );
        $required = array('foo');

        try {
            $this->service->checkRequirements($params, $required);
            $this->assertTrue(true, 'Validation ok');
        } catch (\Exception $e) {
            $this->fail('Unexpected exception catched: ' . $e->getMessage());
        }
    }

    public function testCheckRequirementsTimestamp()
    {
        $params   = array(
            'oauth_timestamp' => -1 * time()
        );

        try {
            $this->service->checkRequirements($params);
            $this->fail('Should throw an exception');
        } catch (\Exception $e) {
            $this->assertTrue(true, 'Exception catched');
            $this->assertEquals(400, $e->getStatusCode(), 'Status code should be 400');
            $this->assertEquals(OAuthServerService::ERROR_TIMESTAMP_REFUSED, $e->getMessage(), 'Check message');
        }
    }

    public function testCheckRequirementsVersion()
    {
        $params   = array(
            'oauth_timestamp' => time(),
            'oauth_version'   => OAuthServerService::OAUTH_VERSION
        );

        try {
            $this->service->checkRequirements($params);
            $this->assertTrue(true, 'Validation ok');
        } catch (\Exception $e) {
            $this->fail('Unexpected exception catched: ' . $e->getMessage());
        }

        $params   = array(
            'oauth_timestamp' => time(),
            'oauth_version'   => '2.0'
        );

        try {
            $this->service->checkRequirements($params);
            $this->fail('Should throw an exception');
        } catch (\Exception $e) {
            $this->assertTrue(true, 'Exception catched');
            $this->assertEquals(400, $e->getStatusCode(), 'Status code should be 400');
            $this->assertEquals(OAuthServerService::ERROR_VERSION_REJECTED, $e->getMessage(), 'Check message');
        }
    }
}

class OAuthServerServiceMock extends OAuthServerService
{
    public function __construct()
    {
    }

    public function checkConsumer($consumer)
    {
        return parent::checkConsumer($consumer);
    }

    public function checkRequirements($requestParameters, array $requiredParameters = array())
    {
        return parent::checkRequirements($requestParameters, $requiredParameters);
    }
}
