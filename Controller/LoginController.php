<?php

namespace Bazinga\OAuthServerBundle\Controller;

use Bazinga\OAuthServerBundle\Model\RequestTokenInterface;
use Bazinga\OAuthServerBundle\Model\Provider\TokenProviderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
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
    private $engine;

    /**
     * @var TokenProviderInterface
     */
    private $tokenProvider;

    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    /**
     * @var AuthorizationCheckerInterface
     */
    private $authorizationChecker;

    /**
     * LoginController constructor.
     *
     * @param EngineInterface               $engine
     * @param TokenProviderInterface        $tokenProvider
     * @param TokenStorageInterface         $tokenStorage
     * @param AuthorizationCheckerInterface $authorizationChecker
     */
    public function __construct(EngineInterface $engine, TokenProviderInterface $tokenProvider, TokenStorageInterface $tokenStorage, AuthorizationCheckerInterface $authorizationChecker)
    {
        $this->engine               = $engine;
        $this->tokenProvider        = $tokenProvider;
        $this->tokenStorage         = $tokenStorage;
        $this->authorizationChecker = $authorizationChecker;
    }

    /**
     * Present a form to the user to accept or not to share
     * its information with the consumer.
     */
    public function allowAction(Request $request)
    {
        $oauth_token    = $request->get('oauth_token', null);
        $oauth_callback = $request->get('oauth_callback', null);

        if ($this->authorizationChecker->isGranted('IS_AUTHENTICATED_FULLY')) {
            $token = $this->tokenProvider->loadRequestTokenByToken($oauth_token);

            if ($token instanceof RequestTokenInterface) {
                $this->tokenProvider->setUserForRequestToken($token, $this->tokenStorage->getToken()->getUser());

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
