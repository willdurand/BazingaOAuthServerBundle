<?php

namespace Bazinga\OAuthBundle\EventListener;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * @package     BazingaOAuthBundle
 * @subpackage  EventListener
 * @author William DURAND <william.durand1@gmail.com>
 */
class OAuthExceptionListener
{
    /**
     * {@inheritdoc}
     */
    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        $exception = $event->getException();

        if (! $exception instanceof HttpException) {
            return;
        }

        $response = new Response();

        $response->setStatusCode($exception->getStatusCode());
        $response->setContent($exception->getMessage());

        $event->setResponse($response);
    }
}
