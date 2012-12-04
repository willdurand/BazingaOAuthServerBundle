<?php

namespace Bazinga\OAuthServerBundle\Controller;

use Bazinga\OAuthServerBundle\Model\OAuthRequestTokenInterface;
use Bazinga\OAuthServerBundle\Model\Provider\OAuthTokenProviderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\Templating\EngineInterface;

/**
 * LoginController: this controller must be secured to get a valid user.
 *
 * @author William DURAND <william.durand1@gmail.com>
 */
class LoginController
{
    /**
     * @var EngineInterface
     */
    protected $engine;

    /**
     * @var SecurityContextInterface
     */
    protected $securityContext;

    /**
     * @var Request
     */
    protected $request;

    /**
     * @var OAuthTokenProviderInterface
     */
    protected $tokenProvider;

    /**
     * @param EngineInterface             $engine          The template engine.
     * @param SecurityContextInterface    $securityContext The security context.
     * @param Request                     $request         The request.
     * @param OAuthTokenProviderInterface $tokenProvider   The OAuth token provider.
     */
    public function __construct(EngineInterface $engine, SecurityContextInterface $securityContext, Request $request, OAuthTokenProviderInterface $tokenProvider)
    {
        $this->engine  = $engine;
        $this->request = $request;
        $this->securityContext = $securityContext;
        $this->tokenProvider   = $tokenProvider;
    }

    /**
     * Present a form to the user to accept or not to share
     * its information with the consumer.
     */
    public function allowAction()
    {
        $oauth_token    = $this->request->get('oauth_token', null);
        $oauth_callback = $this->request->get('oauth_callback', null);

        if ($this->securityContext->isGranted('IS_AUTHENTICATED_FULLY')) {
            $token = $this->tokenProvider->loadRequestTokenByToken($oauth_token);

            if ($token instanceof OAuthRequestTokenInterface) {
                $this->tokenProvider->setUserForRequestToken($token, $this->securityContext->getToken()->getUser());

                return new Response($this->engine->render('BazingaOAuthServerBundle::authorize.html.twig', array(
                    'consumer'       => $token->getConsumer(),
                    'oauth_token'    => $oauth_token,
                    'oauth_callback' => $oauth_callback
                )));
            }
        }

        throw new HttpException(404);
    }

    public function loginCheckAction()
    {
    }
}
