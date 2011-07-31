<?php

namespace Bazinga\OAuthServerBundle\Security\Firewall;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\Security\Core\Authentication\AuthenticationManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Firewall\ListenerInterface;
use Symfony\Component\Security\Core\SecurityContextInterface;

use Bazinga\OAuthServerBundle\Security\Authentification\Token\OAuthToken;

/**
 * OAuthListener class.
 *
 * @package     BazingaOAuthServerBundle
 * @subpackage  Security
 * @author William DURAND <william.durand1@gmail.com>
 */
class OAuthListener implements ListenerInterface
{
    /**
     * @var \Symfony\Component\Security\Core\SecurityContextInterface
     */
    protected $securityContext;
    /**
     * @var \Symfony\Component\Security\Core\Authentication\AuthenticationManagerInterface
     */
    protected $authenticationManager;

    /**
     * @param \Symfony\Component\Security\Core\SecurityContextInterface $securityContext    The security context.
     * @param \Symfony\Component\Security\Core\Authentication\AuthenticationManagerInterface $authenticationManager The authentification manager.
     */
    public function __construct(SecurityContextInterface $securityContext, AuthenticationManagerInterface $authenticationManager)
    {
        $this->securityContext = $securityContext;
        $this->authenticationManager = $authenticationManager;
    }

    /**
     * @param \Symfony\Component\HttpKernel\Event\GetResponseEvent $event   The event.
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
            } else if ($returnValue instanceof Response) {
                return $event->setResponse($response);
            }
        } catch (AuthenticationException $e) {
            throw $e;
        }

        throw new HttpException(401);
    }
}
