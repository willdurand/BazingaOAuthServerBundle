<?php

namespace Bazinga\OAuthServerBundle\Propel\Provider;

use Bazinga\OAuthServerBundle\Model\Provider\TokenProvider as BaseTokenProvider;
use Bazinga\OAuthServerBundle\Model\TokenInterface;

/**
 * @author Robin van der Vleuten <robinvdvleuten@gmail.com>
 */
class TokenProvider extends BaseTokenProvider
{
    /**
     * {@inheritDoc}
     */
    public function loadRequestTokenBy(array $criteria)
    {
        $query = $this->createRequestTokenQuery();

        foreach ($criteria as $field => $value) {
            $method = 'filterBy'.ucfirst($field);
            $query->$method($value);
        }

        return $query->findOne();
    }

    /**
     * {@inheritDoc}
     */
    public function loadRequestTokens()
    {
        return $this->createRequestTokenQuery()->find();
    }

    /**
     * {@inheritDoc}
     */
    public function loadAccessTokenBy(array $criteria)
    {
        $query = $this->createAccessTokenQuery();

        foreach ($criteria as $field => $value) {
            $method = 'filterBy'.ucfirst($field);
            $query->$method($value);
        }

        return $query->findOne();
    }

    /**
     * {@inheritDoc}
     */
    public function loadAccessTokens()
    {
        return $this->createAccessTokenQuery()->find();
    }

    /**
     * {@inheritDoc}
     */
    public function deleteToken(TokenInterface $token)
    {
        if (!$token instanceof \Persistent) {
            throw new \InvalidArgumentException('This token instance is not supported by the Propel ConsumerProvider implementation');
        }

        $token->delete();
    }

    /**
     * {@inheritDoc}
     */
    public function updateToken(TokenInterface $token)
    {
        if (!$token instanceof \Persistent) {
            throw new \InvalidArgumentException('This token instance is not supported by the Propel ConsumerProvider implementation');
        }

        $token->save();
    }

    protected function createRequestTokenQuery()
    {
        return \PropelQuery::from($this->getRequestTokenClass());
    }

    protected function createAccessTokenQuery()
    {
        return \PropelQuery::from($this->getAccessTokenClass());
    }
}
