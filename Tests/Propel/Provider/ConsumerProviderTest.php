<?php

namespace Bazinga\OAuthServerBundle\Tests\Propel\Provider;

use Bazinga\OAuthServerBundle\Propel\Provider\ConsumerProvider;
use Bazinga\OAuthServerBundle\Tests\TestCase;

/**
 * @author Robin van der Vleuten <robinvdvleuten@gmail.com>
 */
class ConsumerProviderTest extends TestCase
{
    const CONSUMER_CLASS = 'Bazinga\OAuthServerBundle\Propel\Consumer';

    /**
     * @var ConsumerProvider
     */
    private $consumerProvider;

    /**
     * @var \ModelCriteria
     */
    private $query;

    public function setUp()
    {
        if (!class_exists('Propel')) {
            $this->markTestSkipped('Propel has to be installed for this test to run.');
        }

        $this->query = $this->getMockBuilder('ModelCriteria')
            ->disableOriginalConstructor()
            ->setMethods(array('filterByFoo', 'filterByConsumerKey', 'findOne'))
            ->getMock();

        $this->consumerProvider = $this->getMockBuilder('Bazinga\OAuthServerBundle\Propel\Provider\ConsumerProvider')
            ->setConstructorArgs(array(static::CONSUMER_CLASS))
            ->setMethods(array('createConsumerQuery'))
            ->getMock();

        $this->consumerProvider->expects($this->any())
            ->method('createConsumerQuery')
            ->will($this->returnValue($this->query));
    }

    public function testGetClass()
    {
        $this->assertEquals(static::CONSUMER_CLASS, $this->consumerProvider->getConsumerClass());
    }

    public function testGetConsumerBy()
    {
        $criteria = array('foo' => 'bar');

        $this->query->expects($this->once())
            ->method('filterByFoo')
            ->with($this->equalTo('bar'));

        $this->query->expects($this->once())
            ->method('findOne');

        $this->consumerProvider->getConsumerBy($criteria);
    }

    public function testGetConsumerByKey()
    {
        $consumerKey = 'bar';

        $this->query->expects($this->once())
            ->method('filterByConsumerKey')
            ->with($this->equalTo('bar'));

        $this->query->expects($this->once())
            ->method('findOne');

        $this->consumerProvider->getConsumerByKey($consumerKey);
    }

    public function testUpdateConsumer()
    {
        $consumer = $this->getMock('Bazinga\OAuthServerBundle\Propel\Consumer');

        $consumer->expects($this->once())
            ->method('save');

        $this->consumerProvider->updateConsumer($consumer);
    }

    public function testUpdateNonPropelConsumerErrors()
    {
        $token = $this->getMock('Bazinga\OAuthServerBundle\Model\ConsumerInterface');

        try {
            $this->consumerProvider->updateConsumer($token);
            $this->fail('->updateConsumer() throws an InvalidArgumentException because the consumer instance is not supported by the Propel ConsumerProvider implementation');
        } catch (\Exception $e) {
            $this->assertInstanceof('InvalidArgumentException', $e, '->updateConsumer() throws an InvalidArgumentException because the consumer instance is not supported by the Propel ConsumerProvider implementation');
        }
    }

    public function testDeleteConsumer()
    {
        $consumer = $this->getMock('Bazinga\OAuthServerBundle\Propel\Consumer');

        $consumer->expects($this->once())
            ->method('delete');

        $this->consumerProvider->deleteConsumer($consumer);
    }

    public function testDeleteNonPropelConsumerErrors()
    {
        $token = $this->getMock('Bazinga\OAuthServerBundle\Model\ConsumerInterface');

        try {
            $this->consumerProvider->deleteConsumer($token);
            $this->fail('->deleteConsumer() throws an InvalidArgumentException because the consumer instance is not supported by the Propel ConsumerProvider implementation');
        } catch (\Exception $e) {
            $this->assertInstanceof('InvalidArgumentException', $e, '->deleteConsumer() throws an InvalidArgumentException because the consumer instance is not supported by the Propel ConsumerProvider implementation');
        }
    }
}
