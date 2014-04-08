<?php

namespace Bazinga\OAuthServerBundle\Propel\Provider;

use Bazinga\OAuthServerBundle\Model\ConsumerInterface;
use Bazinga\OAuthServerBundle\Model\Provider\ConsumerProvider as BaseConsumerProvider;

/**
 * @author Robin van der Vleuten <robinvdvleuten@gmail.com>
 */
class ConsumerProvider extends BaseConsumerProvider
{
    /**
     * {@inheritDoc}
     */
    public function getConsumerBy(array $criteria)
    {
        $query = $this->createConsumerQuery();

        foreach ($criteria as $field => $value) {
            $method = 'filterBy'.ucfirst($field);
            $query->$method($value);
        }

        return $query->findOne();
    }

    /**
     * {@inheritDoc}
     */
    public function deleteConsumer(ConsumerInterface $consumer)
    {
        if (!$consumer instanceof \Persistent) {
            throw new \InvalidArgumentException('This consumer instance is not supported by the Propel ConsumerProvider implementation');
        }

        $consumer->delete();
    }

    /**
     * {@inheritDoc}
     */
    public function updateConsumer(ConsumerInterface $consumer)
    {
        if (!$consumer instanceof \Persistent) {
            throw new \InvalidArgumentException('This consumer instance is not supported by the Propel ConsumerProvider implementation');
        }

        $consumer->save();
    }

    protected function createConsumerQuery()
    {
        return \PropelQuery::from($this->getConsumerClass());
    }
}
