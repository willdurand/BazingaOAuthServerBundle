<?php

namespace Bazinga\OAuthServerBundle\Security\Firewall;

use Bazinga\OAuthServerBundle\Security\Authentification\Token\OAuthToken;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\Security\Core\Authentication\AuthenticationManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Firewall\ListenerInterface;
use Symfony\Component\Security\Core\SecurityContextInterface;

/**
 * @author William DURAND <william.durand1@gmail.com>
 */
class OAuthListener implements ListenerInterface
{
    /**
     * @var SecurityContextInterface
     */
    protected $securityContext;

    /**
     * @var AuthenticationManagerInterface
     */
    protected $authenticationManager;

    /**
     * @param SecurityContextInterface       $securityContext       The security context.
     * @param AuthenticationManagerInterface $authenticationManager The authentification manager.
     */
    public function __construct(SecurityContextInterface $securityContext, AuthenticationManagerInterface $authenticationManager)
    {
        $this->securityContext = $securityContext;
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
                return $this->securityContext->setToken($returnValue);
            } elseif ($returnValue instanceof Response) {
                return $event->setResponse($returnValue);
            }
        } catch (AuthenticationException $e) {
            throw $e;
        }

        throw new HttpException(401);
    }
}
