<?php

namespace Bazinga\OAuthServerBundle\EventListener;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;

/**
 * Called early, this listener will add some oauth attributes to the Request if
 * the current Request is an OAuth request (by checking parameters).
 *
 * @author William DURAND <william.durand1@gmail.com>
 */
class OAuthRequestListener
{
    /**
     * {@inheritdoc}
     */
    public function onEarlyKernelRequest(GetResponseEvent $event)
    {
        if (HttpKernelInterface::MASTER_REQUEST !== $event->getRequestType()) {
            return;
        }

        $request    = $event->getRequest();
        $parameters = $this->filterRequestParameters($request);

        // check if it's an oauth request or not
        if (false === array_key_exists('oauth_token', $parameters)&&
            false === array_key_exists('oauth_consumer_key', $parameters)
        ) {
            return;
        }

        $request->attributes->set('oauth_request_parameters', $parameters);
        $request->attributes->set('oauth_request_method', $request->getMethod());
        $request->attributes->set('oauth_request_url', $this->buildRequestUrl($request));
    }

    /**
     * Merge all acceptable parameters.
     *
     * @param  Request $request The request.
     * @return array
     */
    protected function filterRequestParameters(Request $request)
    {
        return array_replace(
            $this->parseAuthorizationHeader($request),
            $request->query->all(),
            $request->request->all()
        );
    }

    /**
     * Parse the Authorization header if available.
     *
     * @param  Request $request The request.
     * @return array
     */
    protected function parseAuthorizationHeader(Request $request)
    {
        $authorization = null;

        if (!$request->headers->has('authorization')) {
            // The Authorization header may not be passed to PHP by Apache;
            // Trying to obtain it through apache_request_headers()
            if (function_exists('apache_request_headers')) {
                $headers = apache_request_headers();

                // Server-side fix for bug in old Android versions (a nice side-effect of this fix means we don't care
                // about capitalization for Authorization).
                $headers = array_combine(array_map('ucwords', array_keys($headers)), array_values($headers));

                if (isset($headers['Authorization'])) {
                    $authorization = $headers['Authorization'];
                }
            }
        } else {
            $authorization = $request->headers->get('authorization');
        }

        $params = array();

        if (!$authorization) {
            return $params;
        }

        // Remove 'OAuth' string
        $authorization = substr($authorization, 6);

        foreach (preg_split('#,#', $authorization) as $parameter) {
            $split = preg_split('#=#', $parameter);

            if (2 !== count($split)) {
                continue;
            }

            $key   = rawurldecode(trim($split[0]));
            $value = rawurldecode(str_replace('"', '', trim($split[1])));

            $params[$key] = $value;
        }

        return $params;
    }

    /**
     * Build a valid Request URL based on the Request.
     * @see http://oauth.net/core/1.0/#sig_url
     *
     * @param  Request $request The request.
     * @return string
     */
    protected function buildRequestUrl(Request $request)
    {
        return sprintf('%s://%s%s%s',
            $request->getScheme(),
            $request->getHttpHost(),
            $request->getBaseUrl(),
            $request->getPathInfo()
        );
    }
}
