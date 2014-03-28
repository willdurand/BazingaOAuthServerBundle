<?php

namespace Bazinga\OAuthServerBundle\Model\Provider;

use Bazinga\OAuthServerBundle\Model\AccessTokenInterface;
use Bazinga\OAuthServerBundle\Model\ConsumerInterface;
use Bazinga\OAuthServerBundle\Model\RequestTokenInterface;
use Bazinga\OAuthServerBundle\Model\UserInterface;
use Bazinga\OAuthServerBundle\Util\Random;

/**
 * @author Robin van der Vleuten <robinvdvleuten@gmail.com>
 */
abstract class TokenProvider implements TokenProviderInterface
{
    /**
     * {@inheritDoc}
     */
    public function createRequestToken(ConsumerInterface $consumer)
    {
        $class = $this->getRequestTokenClass();

        $requestToken = new $class;
        $requestToken->setToken(Random::generateToken());
        $requestToken->setSecret(Random::generateToken());
        $requestToken->setExpiresAt(\DateTime::createFromFormat('U', time() + 3600));
        $requestToken->setVerifier(Random::generateToken());
        $requestToken->setConsumer($consumer);

        $this->updateToken($requestToken);

        return $requestToken;
    }

    /**
     * {@inheritDoc}
     */
    public function loadRequestTokenByToken($oauth_token)
    {
        return $this->loadRequestTokenBy(array('token' => $oauth_token));
    }

    /**
     * {@inheritDoc}
     */
    public function setUserForRequestToken(RequestTokenInterface $requestToken, UserInterface $user)
    {
        $requestToken->setUser($user);

        $this->updateToken($requestToken);
    }

    /**
     * {@inheritDoc}
     */
    public function deleteRequestToken(RequestTokenInterface $requestToken)
    {
        $this->deleteToken($requestToken);
    }

    /**
     * {@inheritDoc}
     */
    public function createAccessToken(ConsumerInterface $consumer, UserInterface $user)
    {
        $class = $this->getAccessTokenClass();

        $accessToken = new $class;
        $accessToken->setToken(Random::generateToken());
        $accessToken->setSecret(Random::generateToken());
        $accessToken->setConsumer($consumer);
        $accessToken->setUser($user);

        $this->updateToken($accessToken);

        return $accessToken;
    }

    /**
     * {@inheritDoc}
     */
    public function loadAccessTokenByToken($oauth_token)
    {
        return $this->loadAccessTokenBy(array('token' => $oauth_token));
    }

    /**
     * {@inheritDoc}
     */
    public function deleteAccessToken(AccessTokenInterface $accessToken)
    {
        $this->deleteToken($accessToken);
    }

    /**
     * @return int The number of tokens deleted.
     */
    public function deleteExpired()
    {
        $tokens = array_merge($this->loadRequestTokens(), $this->loadAccessTokens());

        foreach ($tokens as $token) {
            if ($token->hasExpired()) {
                $this->deleteToken($token);
            }
        }
    }
}
