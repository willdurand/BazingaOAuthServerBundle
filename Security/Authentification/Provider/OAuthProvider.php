<?php

namespace Bazinga\OAuthBundle\Security\Authentification\Provider;

use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\Security\Core\Authentication\Provider\AuthenticationProviderInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserProviderInterface;

use Bazinga\OAuthBundle\Model\Provider\OAuthTokenProviderInterface;
use Bazinga\OAuthBundle\Security\Authentification\Token\OAuthToken;
use Bazinga\OAuthBundle\Service\OAuthServerServiceInterface;

/**
 * OAuthProvider class.
 *
 * @package     BazingaOAuthBundle
 * @subpackage  Security
 * @author William DURAND <william.durand1@gmail.com>
 */
class OAuthProvider implements AuthenticationProviderInterface
{
    /**
     * @var \Symfony\Component\Security\Core\User\UserProviderInterface
     */
    protected $userProvider;
    /**
     * @var \Bazinga\OAuthBundle\Service\OAuthServerServiceInterface
     */
    protected $serverService;
    /**
     * @var \Bazinga\OAuthBundle\Model\Provider\OAuthTokenProviderInterface
     */
    protected $tokenProvider;

    /**
     * @param \Symfony\Component\Security\Core\User\UserProviderInterface $userProvider      The user provider.
     * @param \Bazinga\OAuthBundle\Service\OAuthServerServiceInterface $serverService The OAuth server service.
     */
    public function __construct(UserProviderInterface $userProvider, OAuthServerServiceInterface $serverService)
    {
        $this->userProvider  = $userProvider;
        $this->serverService = $serverService;
        $this->tokenProvider = $serverService->getTokenProvider();
    }

    /**
     * {@inheritdoc}
     */
    public function authenticate(TokenInterface $token)
    {
        if (!$this->supports($token)) {
            return null;
        }

        if ($this->serverService->validateRequest($token->getRequestParameters(), $token->getRequestMethod(), $token->getRequestUrl())) {
            $params      = $token->getRequestParameters();
            $accessToken = $this->tokenProvider->loadAccessTokenByToken($params['oauth_token']);
            $user        = $accessToken->getUser();

            if (null !== $user) {
                $token->setUser($user);
                return $token;
            }
        }

        throw new AuthenticationException('OAuth authentification failed');
    }

    /**
     * {@inheritdoc}
     */
    public function supports(TokenInterface $token)
    {
        return ($token instanceof OAuthToken);
    }
}
