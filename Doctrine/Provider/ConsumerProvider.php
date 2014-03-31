<?php

namespace Bazinga\OAuthServerBundle\Doctrine\Provider;

use Bazinga\OAuthServerBundle\Model\Provider\ConsumerProvider as BaseConsumerProvider;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\Persistence\ObjectRepository;

/**
 * @author Robin van der Vleuten <robinvdvleuten@gmail.com>
 */
class ConsumerProvider extends BaseConsumerProvider
{
    /**
     * @var string
     */
    private $class;

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
     * @param string        $class
     */
    public function __construct(ObjectManager $objectManager, $class)
    {
        $this->objectManager = $objectManager;
        $this->class = $class;

        $this->repository = $objectManager->getRepository($class);
    }

    /**
      * {@inheritDoc}
      */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * {@inheritDoc}
     */
    public function getConsumerBy(array $criteria)
    {
        return $this->repository->findOneBy($criteria);
    }
}
