<?php

namespace Bazinga\OAuthServerBundle\Security\Firewall;

use Bazinga\OAuthServerBundle\Security\Authentification\Token\OAuthToken;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Security\Core\Authentication\AuthenticationManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Firewall\ListenerInterface;

/**
 * @author William DURAND <william.durand1@gmail.com>
 */
class OAuthListener implements ListenerInterface
{
    /**
     * @var TokenStorageInterface
     */
    protected $tokenStorage;

    /**
     * @var AuthenticationManagerInterface
     */
    protected $authenticationManager;

    /**
     * @param TokenStorageInterface          $securityContext       The security context.
     * @param AuthenticationManagerInterface $authenticationManager The authentification manager.
     */
    public function __construct(TokenStorageInterface $tokenStorage, AuthenticationManagerInterface $authenticationManager)
    {
        $this->tokenStorage          = $tokenStorage;
        $this->authenticationManager = $authenticationManager;
    }

    /**
     * @param GetResponseEvent $event The event.
     */
    public function handle(GetResponseEvent $event)
    {
        $request = $event->getRequest();

        if (false === $request->attributes->get('oauth_request_parameters')) {
            return;
        }

        $token = new OAuthToken();
        $token->setRequestParameters($request->attributes->get('oauth_request_parameters'));
        $token->setRequestMethod($request->attributes->get('oauth_request_method'));
        $token->setRequestUrl($request->attributes->get('oauth_request_url'));

        try {
            $returnValue = $this->authenticationManager->authenticate($token);

            if ($returnValue instanceof TokenInterface) {
                return $this->tokenStorage->setToken($returnValue);
            } elseif ($returnValue instanceof Response) {
                return $event->setResponse($returnValue);
            }
        } catch (AuthenticationException $e) {
            throw $e;
        }

        throw new HttpException(401);
    }
}
