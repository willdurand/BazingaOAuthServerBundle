<?php

namespace Bazinga\OAuthBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Templating\EngineInterface;

use Bazinga\OAuthBundle\Service\OAuthServerServiceInterface;
use Bazinga\OAuthBundle\Model\OAuthRequestTokenInterface;

/**
 * ServerController class.
 *
 * @package     BazingaOAuthBundle
 * @subpackage  Controller
 * @author William DURAND <william.durand1@gmail.com>
 */
class ServerController
{
    /**
     * @var \Symfony\Component\Routing\RouterInterface
     */
    protected $router;
    /**
     * @var \Symfony\Component\Templating\EngineInterface
     */
    protected $engine;
    /**
     * @var \Symfony\Component\HttpFoundation\Request
     */
    protected $request;
    /**
     * @var \Bazinga\OAuthBundle\Service\OAuthServerInterface
     */
    protected $serverService;

    /**
     * Default constructor.
     * @param \Symfony\Component\Routing\RouterInterface $router                                    The router.
     * @param \Symfony\Component\Templating\EngineInterface $engine                                 The template engine.
     * @param \Symfony\Component\HttpFoundation\Request $request                                    The request.
     * @param \Bazinga\OAuthBundle\Service\OAuthServerServiceInterface $serverService        The OAuth server service.
     */
    public function __construct(RouterInterface $router, EngineInterface $engine, Request $request, OAuthServerServiceInterface $serverService)
    {
        $this->router  = $router;
        $this->engine  = $engine;
        $this->request = $request;
        $this->serverService = $serverService;
    }

    /**
     * Get a request token.
     * OAuth v1.0
     */
    public function requestTokenAction()
    {
        $data = $this->serverService->requestToken(
            $this->request->attributes->get('oauth_request_parameters'),
            $this->request->attributes->get('oauth_request_method'),
            $this->request->attributes->get('oauth_request_url')
        );

        return $this->sendResponse($data);
    }

    /**
     * Get user authorization.
     * OAuth v1.0
     */
    public function authorizeAction()
    {
        $oauth_token    = $this->request->get('oauth_token', null);
        $oauth_callback = $this->request->get('oauth_callback', null);

        $token = $this->serverService->getTokenProvider()->loadRequestTokenByToken($oauth_token);

        if (! $token instanceof OAuthRequestTokenInterface) {
            throw new HttpException(404);
        }

        if ('GET' === $this->request->getMethod()) {
            // redirect to the secured 'allow' page
            return new RedirectResponse(
                $this->router->generate('bazinga_oauth_login_allow', array(
                    'oauth_token'    => $oauth_token,
                    'oauth_callback' => $oauth_callback
                ))
            );
        } else {
            if (false !== $this->request->request->get('submit_true', false)) {
                $authorizeString = $this->serverService->authorize($oauth_token, $oauth_callback);

                if ('http' === substr($authorizeString, 0, 4)) {
                    return new RedirectResponse($authorizeString, 302);
                } else {
                    return $this->sendResponse($authorizeString);
                }
            } else {
                $this->serverService->getTokenProvider()->deleteRequestToken($token);

                // error page if the user didn't accept to share its information.
                return new Response($this->engine->render('BazingaOAuthBundle::error.html.twig', array(
                    'consumer' => $token->getConsumer()
                )));
            }
        }

        throw new HttpException(404);
    }

    /**
     * Exchange a request token for an access token.
     * OAuth v1.0
     */
    public function accessTokenAction()
    {
        $data = $this->serverService->accessToken(
            $this->request->attributes->get('oauth_request_parameters'),
            $this->request->attributes->get('oauth_request_method'),
            $this->request->attributes->get('oauth_request_url')
        );

        return $this->sendResponse($data);
    }

    /**
     * Configure a OAuth compliant Response object.
     *
     * @param string $content    A content to send.
     * @return Response
     */
    protected function sendResponse($content)
    {
        $response = new Response($content);

        $response->headers->set('Content-Length', strlen($response->getContent()));
        $response->headers->set('Content-Type', 'application/x-www-form-urlencoded');

        return $response;
    }
}
