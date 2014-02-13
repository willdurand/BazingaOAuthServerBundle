<?php

namespace Bazinga\OAuthServerBundle\Security\Authentification\Provider;

use Bazinga\OAuthServerBundle\Model\Provider\TokenProviderInterface;
use Bazinga\OAuthServerBundle\Security\Authentification\Token\OAuthToken;
use Bazinga\OAuthServerBundle\Service\OAuthServerServiceInterface;
use Symfony\Component\Security\Core\Authentication\Provider\AuthenticationProviderInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserProviderInterface;

/**
 * @author William DURAND <william.durand1@gmail.com>
 */
class OAuthProvider implements AuthenticationProviderInterface
{
    /**
     * @var UserProviderInterface
     */
    protected $userProvider;

    /**
     * @var OAuthServerServiceInterface
     */
    protected $serverService;

    /**
     * @var TokenProviderInterface
     */
    protected $tokenProvider;

    /**
     * @param UserProviderInterface       $userProvider  The user provider.
     * @param OAuthServerServiceInterface $serverService The OAuth server service.
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
