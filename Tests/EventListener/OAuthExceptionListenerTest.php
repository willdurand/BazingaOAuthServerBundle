<?php

namespace Bazinga\OAuthServerBundle\ Tests\EventListener;

use Bazinga\OAuthServerBundle\Tests\TestCase;

/**
 * @package     BazingaOAuthServerBundle
 * @subpackage  EventListener
 * @author William DURAND <william.durand1@gmail.com>
 */
class OAuthExceptionListenerTest extends TestCase
{
    public function testOnKernelException()
    {
        $listener = $this->getListener();

        $event    = $this->getEvent(400, 'foobar');
        $listener->onKernelException($event);
        $response = $event->getResponse();

        $this->assertInstanceOf('Symfony\Component\HttpFoundation\Response', $response, 'A Response object is expected.');
        $this->assertEquals(400, $response->getStatusCode(), 'Status code is correct.');
        $this->assertEquals('foobar', $response->getContent(), 'Content is the message exception.');

        $event    = $this->getEvent(401, null);
        $listener->onKernelException($event);
        $response = $event->getResponse();

        $this->assertInstanceOf('Symfony\Component\HttpFoundation\Response', $response, 'A Response object is expected.');
        $this->assertEquals(401, $response->getStatusCode(), 'Status code is correct.');
        $this->assertEquals('', $response->getContent(), 'Content is the message exception.');

        $event    = $this->getEvent(404, '');
        $listener->onKernelException($event);
        $response = $event->getResponse();

        $this->assertInstanceOf('Symfony\Component\HttpFoundation\Response', $response, 'A Response object is expected.');
        $this->assertEquals(404, $response->getStatusCode(), 'Status code is correct.');
        $this->assertEquals('', $response->getContent(), 'Content is the message exception.');
    }

    /**
     * @return \Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent
     */
    protected function getEvent($statusCode, $message)
    {
        return new \Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent(
            $this->getMock('Symfony\Component\HttpKernel\HttpKernelInterface'),
            $this->getMock('Symfony\Component\HttpFoundation\Request'),
            null,
            $this->getException($statusCode, $message)
        );
    }

    /**
     * @return \Symfony\Component\HttpKernel\Exception\HttpException
     */
    protected function getException($statusCode, $message)
    {
        return new \Symfony\Component\HttpKernel\Exception\HttpException($statusCode, $message);
    }

    /**
     * @return \Bazinga\OAuthServerBundle\EventListener\OAuthExceptionListener
     */
    protected function getListener()
    {
        return new \Bazinga\OAuthServerBundle\EventListener\OAuthExceptionListener();
    }
}
