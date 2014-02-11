<?php

namespace Bazinga\OAuthServerBundle\Model;

/**
 * @author Robin van der Vleuten <robinvdvleuten@gmail.com>
 */
class Consumer implements ConsumerInterface
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $consumerKey;

    /**
     * @var string
     */
    protected $consumerSecret;

    /**
     * @var string
     */
    protected $callback;

    /**
     * {@inheritDoc}
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * {@inheritDoc}
     */
    public function setConsumerKey($consumerKey)
    {
        $this->consumerKey = $consumerKey;
    }

    /**
     * {@inheritDoc}
     */
    public function getConsumerKey()
    {
        return $this->consumerKey;
    }

    /**
     * {@inheritDoc}
     */
    public function setConsumerSecret($consumerSecret)
    {
        $this->consumerSecret = $consumerSecret;
    }

    /**
     * {@inheritDoc}
     */
    public function getConsumerSecret()
    {
        return $this->consumerSecret;
    }

    /**
     * {@inheritDoc}
     */
    public function setCallback($callback)
    {
        $this->callback = $callback;
    }

    /**
     * {@inheritDoc}
     */
    public function getCallback()
    {
        return $this->callback;
    }
}
