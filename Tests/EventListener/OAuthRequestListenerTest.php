<?php

namespace Bazinga\OAuthServerBundle\Tests\EventListener;

use Symfony\Component\HttpFoundation\Request;

use Bazinga\OAuthServerBundle\Tests\TestCase;
use Bazinga\OAuthServerBundle\EventListener\OAuthRequestListener;

/**
 * @package     BazingaOAuthServerBundle
 * @subpackage  EventListener
 * @author William DURAND <william.durand1@gmail.com>
 */
class OAuthRequestListenerTest extends TestCase
{
    /**
     * @var \Bazinga\OAuthServerBundle\EventListener\OAuthRequestListener
     */
    protected $listener;
    /**
     * @var \Symfony\Component\HttpFoundation\Request
     */
    protected $request;

    public function setUp()
    {
        $this->listener = new OAuthRequestListenerMock();
        $this->request  = new Request();
    }

    public function testParseAuthorizationHeader()
    {
        $this->request->headers->set('Authorization', 'OAuth foo=bar,baz="foobaz",name=will');

        $headers = $this->listener->parseAuthorizationHeader($this->request);

        $this->assertTrue(is_array($headers), 'Result is an array');
        $this->assertEquals(3, count($headers), 'Result must contains 3 elements');
        $this->assertArrayHasKey('foo', $headers, 'Check keys');
        $this->assertArrayHasKey('baz', $headers, 'Check keys');
        $this->assertArrayHasKey('name', $headers, 'Check keys');
        $this->assertEquals('bar', $headers['foo'], 'Check normal value');
        $this->assertEquals('foobaz', $headers['baz'], 'Check value with quotes');
        $this->assertEquals('will', $headers['name'], 'Check normal value');
    }

    public function testParseAuthorizationHeaderWithoutAuthorization()
    {
        $headers = $this->listener->parseAuthorizationHeader($this->request);

        $this->assertTrue(is_array($headers), 'Result is an array');
        $this->assertEquals(0, count($headers), 'Result should not contain any element');
    }

    public function testParseAuthorizationHeaderWithNullValue()
    {
        $headers = $this->listener->parseAuthorizationHeader($this->request);
        $this->request->headers->set('Authorization', null);

        $this->assertTrue(is_array($headers), 'Result is an array');
        $this->assertEquals(0, count($headers), 'Result should not contain any element');
    }

    public function testParseAuthorizationHeaderWithEmptyValue()
    {
        $headers = $this->listener->parseAuthorizationHeader($this->request);
        $this->request->headers->set('Authorization', '');

        $this->assertTrue(is_array($headers), 'Result is an array');
        $this->assertEquals(0, count($headers), 'Result should not contain any element');
    }

    public function testBuildRequestUrl()
    {
        $request    = Request::create('http://test.com/foo?bar=baz');
        $requestUrl = $this->listener->buildRequestUrl($request);

        $this->assertEquals('http://test.com/foo', $requestUrl, '');

        $request    = Request::create('http://test.com');
        $requestUrl = $this->listener->buildRequestUrl($request);

        $this->assertEquals('http://test.com/', $requestUrl, '');

        $request    = Request::create('http://test.com');
        $requestUrl = $this->listener->buildRequestUrl($request);

        $this->assertEquals('http://test.com/', $requestUrl, '');

        $request    = Request::create('https://test.com');
        $requestUrl = $this->listener->buildRequestUrl($request);

        $this->assertEquals('https://test.com/', $requestUrl, '');
    }
}

/**
 * Mocked class that allows to change method visibility.
 */
class OAuthRequestListenerMock extends OAuthRequestListener
{
    public function parseAuthorizationHeader(Request $request)
    {
        return parent::parseAuthorizationHeader($request);
    }

    public function buildRequestUrl(Request $request)
    {
        return parent::buildRequestUrl($request);
    }
}
