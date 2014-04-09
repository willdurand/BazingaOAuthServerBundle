<?php

namespace Bazinga\OAuthServerBundle\Tests\Propel\Provider;

use Bazinga\OAuthServerBundle\Tests\TestCase;

/**
 * @author Robin van der Vleuten <robinvdvleuten@gmail.com>
 */
class TokenProviderTest extends TestCase
{
    const TOKEN_CLASS = 'Bazinga\OAuthServerBundle\Propel\Token';

    const REQUEST_TOKEN_CLASS = 'Bazinga\OAuthServerBundle\Propel\RequestToken';

    const ACCESS_TOKEN_CLASS = 'Bazinga\OAuthServerBundle\Propel\AccessToken';

    /**
     * @var \ModelCriteria
     */
    private $query;

    /**
     * @var \Bazinga\OAuthServerBundle\Propel\Provider\TokenProvider
     */
    private $tokenProvider;

    public function setUp()
    {
        if (!class_exists('Propel')) {
            $this->markTestSkipped('Propel has to be installed for this test to run.');
        }

        $this->query = $this->getMockBuilder('\ModelCriteria')
            ->disableOriginalConstructor()
            ->setMethods(array('filterByFoo', 'filterByToken', 'find', 'findOne'))
            ->getMock();

        $this->tokenProvider = $this->getMockBuilder('Bazinga\OAuthServerBundle\Propel\Provider\TokenProvider')
            ->setConstructorArgs(array(self::REQUEST_TOKEN_CLASS, self::ACCESS_TOKEN_CLASS))
            ->setMethods(array('createRequestTokenQuery', 'createAccessTokenQuery'))
            ->getMock();

        $this->tokenProvider->expects($this->any())
            ->method('createRequestTokenQuery')
            ->will($this->returnValue($this->query));

        $this->tokenProvider->expects($this->any())
            ->method('createAccessTokenQuery')
            ->will($this->returnValue($this->query));
    }

    public function testGetRequestTokenClass()
    {
        $this->assertEquals(self::REQUEST_TOKEN_CLASS, $this->tokenProvider->getRequestTokenClass());
    }

    public function testGetAccessTokenClass()
    {
        $this->assertEquals(self::ACCESS_TOKEN_CLASS, $this->tokenProvider->getAccessTokenClass());
    }

    public function testLoadRequestTokenBy()
    {
        $criteria = array('foo' => 'bar');

        $this->query->expects($this->once())
            ->method('filterByFoo')
            ->with($this->equalTo('bar'))
            ->will($this->returnValue(array()));

        $this->tokenProvider->loadRequestTokenBy($criteria);
    }

    public function testLoadRequestTokenByToken()
    {
        $token = 'foo';

        $this->query->expects($this->once())
            ->method('filterByToken')
            ->with($this->equalTo($token))
            ->will($this->returnValue(array()));

        $this->tokenProvider->loadRequestTokenByToken($token);
    }

    public function testLoadRequestTokens()
    {
        $this->query->expects($this->once())
            ->method('find');

        $this->tokenProvider->loadRequestTokens();
    }

    public function testLoadAccessTokenBy()
    {
        $criteria = array('foo' => 'bar');

        $this->query->expects($this->once())
            ->method('filterByFoo')
            ->with($this->equalTo('bar'))
            ->will($this->returnValue(array()));

        $this->tokenProvider->loadAccessTokenBy($criteria);
    }

    public function testLoadAccessTokenByToken()
    {
        $token = 'foo';

        $this->query->expects($this->once())
            ->method('filterByToken')
            ->with($this->equalTo($token))
            ->will($this->returnValue(array()));

        $this->tokenProvider->loadAccessTokenByToken($token);
    }

    public function testLoadAccessTokens()
    {
        $this->query->expects($this->once())
            ->method('find');

        $this->tokenProvider->loadAccessTokens();
    }

    public function testUpdateToken()
    {
        $token = $this->getMock(self::TOKEN_CLASS);

        $token->expects($this->once())
            ->method('save');

        $this->tokenProvider->updateToken($token);
    }

    public function testUpdateNonPropelTokenErrors()
    {
        $token = $this->getMock('Bazinga\OAuthServerBundle\Model\RequestTokenInterface');

        try {
            $this->tokenProvider->updateToken($token);
            $this->fail('->updateToken() throws an InvalidArgumentException because the token instance is not supported by the Propel TokenProvider implementation');
        } catch (\Exception $e) {
            $this->assertInstanceof('InvalidArgumentException', $e, '->updateToken() throws an InvalidArgumentException because the token instance is not supported by the Propel TokenProvider implementation');
        }
    }

    public function testDeleteToken()
    {
        $token = $this->getMock(self::TOKEN_CLASS);

        $token->expects($this->once())
            ->method('delete');

        $this->tokenProvider->deleteToken($token);
    }

    public function testDeleteNonPropelTokenErrors()
    {
        $token = $this->getMock('Bazinga\OAuthServerBundle\Model\RequestTokenInterface');

        try {
            $this->tokenProvider->deleteToken($token);
            $this->fail('->deleteToken() throws an InvalidArgumentException because the token instance is not supported by the Propel TokenProvider implementation');
        } catch (\Exception $e) {
            $this->assertInstanceof('InvalidArgumentException', $e, '->deleteToken() throws an InvalidArgumentException because the token instance is not supported by the Propel TokenProvider implementation');
        }
    }
}
