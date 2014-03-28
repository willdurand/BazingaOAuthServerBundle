<?php

namespace Bazinga\OAuthServerBundle\Tests\Doctrine\Provider;

use Bazinga\OAuthServerBundle\Doctrine\Provider\ConsumerProvider;
use Bazinga\OAuthServerBundle\Model\Consumer;
use Bazinga\OAuthServerBundle\Tests\TestCase;

/**
 * @author Robin van der Vleuten <robinvdvleuten@gmail.com>
 */
class ConsumerProviderTest extends TestCase
{
    const CONSUMER_CLASS = 'Bazinga\OAuthServerBundle\Model\Consumer';

    /**
     * @var ConsumerProvider
     */
    private $consumerProvider;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $objectManager;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $objectRepository;

    public function setUp()
    {
        if (!interface_exists('Doctrine\Common\Persistence\ObjectManager')) {
            $this->markTestSkipped('Doctrine Common has to be installed for this test to run.');
        }

        $this->objectRepository = $this->getMock('Doctrine\Common\Persistence\ObjectRepository');

        $this->objectManager = $this->getMock('Doctrine\Common\Persistence\ObjectManager');

        $this->objectManager->expects($this->any())
            ->method('getRepository')
            ->with($this->equalTo(static::CONSUMER_CLASS))
            ->will($this->returnValue($this->objectRepository));

        $this->consumerProvider = new ConsumerProvider($this->objectManager, static::CONSUMER_CLASS);
    }

    public function testGetClass()
    {
        $this->assertEquals(static::CONSUMER_CLASS, $this->consumerProvider->getClass());
    }

    public function testGetConsumerBy()
    {
        $criteria = array('foo' => 'bar');

        $this->objectRepository->expects($this->once())
            ->method('findOneBy')
            ->with($this->equalTo($criteria))
            ->will($this->returnValue(array()));

        $this->consumerProvider->getConsumerBy($criteria);
    }

    public function testGetConsumerByKey()
    {
        $consumerKey = 'foo';

        $this->objectRepository->expects($this->once())
            ->method('findOneBy')
            ->with($this->equalTo(array('consumerKey' => $consumerKey)))
            ->will($this->returnValue(array()));

        $this->consumerProvider->getConsumerByKey($consumerKey);
    }
}
