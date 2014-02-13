<?php

namespace Bazinga\OAuthServerBundle\Controller;

use Bazinga\OAuthServerBundle\Service\OAuthServerServiceInterface;
use Bazinga\OAuthServerBundle\Model\RequestTokenInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Templating\EngineInterface;

/**
 * @author William DURAND <william.durand1@gmail.com>
 */
class ServerController
{
    /**
     * @var RouterInterface
     */
    protected $router;

    /**
     * @var EngineInterface
     */
    protected $engine;

    /**
     * @var OAuthServerServiceInterface
     */
    protected $serverService;

    /**
     * @param RouterInterface             $router        The router.
     * @param EngineInterface             $engine        The template engine.
     * @param OAuthServerServiceInterface $serverService The OAuth server service.
     */
    public function __construct(RouterInterface $router, EngineInterface $engine, OAuthServerServiceInterface $serverService)
    {
        $this->router  = $router;
        $this->engine  = $engine;
        $this->serverService = $serverService;
    }

    /**
     * Get a request token.
     * OAuth v1.0
     */
    public function requestTokenAction(Request $request)
    {
        $data = $this->serverService->requestToken(
            $request->attributes->get('oauth_request_parameters'),
            $request->attributes->get('oauth_request_method'),
            $request->attributes->get('oauth_request_url')
        );

        return $this->sendResponse($data);
    }

    /**
     * Get user authorization.
     * OAuth v1.0
     */
    public function authorizeAction(Request $request)
    {
        $oauth_token    = $request->get('oauth_token', null);
        $oauth_callback = $request->get('oauth_callback', null);

        $token = $this->serverService->getTokenProvider()->loadRequestTokenByToken($oauth_token);

        if (!$token instanceof RequestTokenInterface) {
            throw new HttpException(404);
        }

        if ('GET' === $request->getMethod()) {
            // redirect to the secured 'allow' page
            return new RedirectResponse(
                $this->router->generate('bazinga_oauth_login_allow', array(
                    'oauth_token'    => $oauth_token,
                    'oauth_callback' => $oauth_callback,
                ))
            );
        }

        if (false !== $request->request->get('submit_true')) {
            $authorizeString = $this->serverService->authorize($oauth_token, $oauth_callback);

            if ('http' === substr($authorizeString, 0, 4)) {
                return new RedirectResponse($authorizeString, 302);
            } else {
                return $this->sendResponse($authorizeString);
            }
        }

        $this->serverService->getTokenProvider()->deleteRequestToken($token);

        // error page if the user didn't accept to share its information.
        return new Response($this->engine->render('BazingaOAuthServerBundle::error.html.twig', array(
            'consumer' => $token->getConsumer(),
        )));
    }

    /**
     * Exchange a request token for an access token.
     * OAuth v1.0
     */
    public function accessTokenAction(Request $request)
    {
        $data = $this->serverService->accessToken(
            $request->attributes->get('oauth_request_parameters'),
            $request->attributes->get('oauth_request_method'),
            $request->attributes->get('oauth_request_url')
        );

        return $this->sendResponse($data);
    }

    /**
     * Configure a OAuth compliant Response object.
     *
     * @param  string   $content A content to send.
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
