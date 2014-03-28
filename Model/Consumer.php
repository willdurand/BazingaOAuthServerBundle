<?php

namespace Bazinga\OAuthServerBundle\Model;

/**
 * @author Robin van der Vleuten <robinvdvleuten@gmail.com>
 */
abstract class Consumer implements ConsumerInterface
{
    protected $id;

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
    public function getId()
    {
        return $this->id;
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
    public function setName($name)
    {
        $this->name = $name;
        return $this;
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
    public function setConsumerKey($consumerKey)
    {
        $this->consumerKey = $consumerKey;
        return $this;
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
    public function setConsumerSecret($consumerSecret)
    {
        $this->consumerSecret = $consumerSecret;
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getCallback()
    {
        return $this->callback;
    }

    /**
     * {@inheritDoc}
     */
    public function setCallback($callback)
    {
        $this->callback = $callback;
        return $this;
    }
}
