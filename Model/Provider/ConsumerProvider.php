<?php

namespace Bazinga\OAuthServerBundle\Model\Provider;
use Bazinga\OAuthServerBundle\Util\Random;

/**
 * @author Robin van der Vleuten <robinvdvleuten@gmail.com>
 */
abstract class ConsumerProvider implements ConsumerProviderInterface
{
    /**
     * @var string
     */
    private $consumerClass;

    /**
     * Constructor
     *
     * @param string $consumerClass
     */
    public function __construct($consumerClass)
    {
        $this->consumerClass = $consumerClass;
    }

    /**
     * {@inheritDoc}
     */
    public function getConsumerClass()
    {
        return $this->consumerClass;
    }

    /**
     * {@inheritDoc}
     */
    public function createConsumer($name, $callback = null)
    {
        $class = $this->getConsumerClass();

        /** @var \Bazinga\OAuthServerBundle\Model\ConsumerInterface $consumer */
        $consumer = new $class;
        $consumer->setName($name);
        $consumer->setConsumerKey(Random::generateToken());
        $consumer->setConsumerSecret(Random::generateToken());
        $consumer->setCallback($callback);

        $this->updateConsumer($consumer);

        return $consumer;
    }

    /**
     * {@inheritDoc}
     */
    public function getConsumerByKey($consumerKey)
    {
        return $this->getConsumerBy(array('consumerKey' => $consumerKey));
    }
}
