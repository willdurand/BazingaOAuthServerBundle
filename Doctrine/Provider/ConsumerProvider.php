<?php

namespace Bazinga\OAuthServerBundle\Doctrine\Provider;

use Bazinga\OAuthServerBundle\Model\ConsumerInterface;
use Bazinga\OAuthServerBundle\Model\Provider\ConsumerProvider as BaseConsumerProvider;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\Persistence\ObjectRepository;

/**
 * @author Robin van der Vleuten <robinvdvleuten@gmail.com>
 */
class ConsumerProvider extends BaseConsumerProvider
{
    /**
     * @var ObjectManager
     */
    private $objectManager;

    /**
     * ObjectRepository
     */
    private $repository;

    /**
     * Constructor
     *
     * @param ObjectManager $objectManager A ObjectManager instance.
     * @param string        $consumerClass
     */
    public function __construct(ObjectManager $objectManager, $consumerClass)
    {
        $this->objectManager = $objectManager;

        $this->repository = $objectManager->getRepository($consumerClass);

        parent::__construct($consumerClass);
    }

    /**
     * {@inheritDoc}
     */
    public function getConsumerBy(array $criteria)
    {
        return $this->repository->findOneBy($criteria);
    }

    /**
     * {@inheritDoc}
     */
    public function deleteConsumer(ConsumerInterface $consumer)
    {
        $this->objectManager->remove($consumer);
        $this->objectManager->flush();
    }

    /**
     * {@inheritDoc}
     */
    public function updateConsumer(ConsumerInterface $consumer)
    {
        $this->objectManager->persist($consumer);
        $this->objectManager->flush();
    }
}
