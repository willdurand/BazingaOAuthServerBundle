<?php

namespace Bazinga\OAuthServerBundle\Tests\Doctrine\Provider;

use Bazinga\OAuthServerBundle\Doctrine\Provider\TokenProvider;
use Bazinga\OAuthServerBundle\Model\AccessToken;
use Bazinga\OAuthServerBundle\Model\RequestToken;
use Bazinga\OAuthServerBundle\Model\Token;
use Bazinga\OAuthServerBundle\Tests\TestCase;

/**
 * @author Robin van der Vleuten <robinvdvleuten@gmail.com>
 */
class TokenProviderTest extends TestCase
{
    const REQUEST_TOKEN_CLASS = 'Bazinga\OAuthServerBundle\Tests\Doctrine\Provider\DummyRequestToken';

    const ACCESS_TOKEN_CLASS = 'Bazinga\OAuthServerBundle\Tests\Doctrine\Provider\DummyAccessToken';

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $objectRepository;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $objectManager;

    /**
     * @var TokenProvider
     */
    private $tokenProvider;

    public function setUp()
    {
        if (!interface_exists('Doctrine\Common\Persistence\ObjectManager')) {
            $this->markTestSkipped('Doctrine Common has to be installed for this test to run.');
        }

        $this->objectRepository = $this->getMock('Doctrine\Common\Persistence\ObjectRepository');

        $this->objectManager = $this->getMock('Doctrine\Common\Persistence\ObjectManager');

        $this->objectManager->expects($this->at(0))
            ->method('getRepository')
            ->with($this->equalTo(static::REQUEST_TOKEN_CLASS))
            ->will($this->returnValue($this->objectRepository));

        $this->objectManager->expects($this->at(1))
            ->method('getRepository')
            ->with($this->equalTo(static::ACCESS_TOKEN_CLASS))
            ->will($this->returnValue($this->objectRepository));

        $this->tokenProvider = new TokenProvider($this->objectManager, static::REQUEST_TOKEN_CLASS, static::ACCESS_TOKEN_CLASS);
    }

    public function testGetRequestTokenClass()
    {
        $this->assertEquals(static::REQUEST_TOKEN_CLASS, $this->tokenProvider->getRequestTokenClass());
    }

    public function testGetAccessTokenClass()
    {
        $this->assertEquals(static::ACCESS_TOKEN_CLASS, $this->tokenProvider->getAccessTokenClass());
    }

    public function testLoadRequestTokenBy()
    {
        $criteria = array('foo' => 'bar');

        $this->objectRepository->expects($this->once())
            ->method('findOneBy')
            ->with($this->equalTo($criteria))
            ->will($this->returnValue(array()));

        $this->tokenProvider->loadRequestTokenBy($criteria);
    }

    public function testLoadRequestTokenByToken()
    {
        $token = 'foo';

        $this->objectRepository->expects($this->once())
            ->method('findOneBy')
            ->with($this->equalTo(array('token' => $token)))
            ->will($this->returnValue(array()));

        $this->tokenProvider->loadRequestTokenByToken($token);
    }

    public function testLoadRequestTokens()
    {
        $this->objectRepository->expects($this->once())
            ->method('findAll')
            ->will($this->returnValue(array()));

        $this->tokenProvider->loadRequestTokens();
    }

    public function testCreateRequestToken()
    {
        $consumer = $this->getMock('Bazinga\OAuthServerBundle\Model\ConsumerInterface');

        $this->objectManager->expects($this->once())
            ->method('persist')
            ->with($this->isInstanceOf(static::REQUEST_TOKEN_CLASS));

        $this->objectManager->expects($this->once())
            ->method('flush');

        $token = $this->tokenProvider->createRequestToken($consumer);

        $this->assertInstanceOf(static::REQUEST_TOKEN_CLASS, $token);
        $this->assertEquals($consumer, $token->getConsumer());
    }

    public function testDeleteRequestToken()
    {
        $token = new DummyRequestToken();

        $this->objectManager->expects($this->once())
            ->method('remove')
            ->with($this->equalTo($token));

        $this->objectManager->expects($this->once())
            ->method('flush');

        $this->tokenProvider->deleteRequestToken($token);
    }

    public function testSetUserForRequestToken()
    {
        $token = new DummyRequestToken();
        $user = $this->getMock('Symfony\Component\Security\Core\User\UserInterface');

        $this->objectManager->expects($this->once())
            ->method('persist')
            ->with($this->equalTo($token));

        $this->objectManager->expects($this->once())
            ->method('flush');

        $this->tokenProvider->setUserForRequestToken($token, $user);

        $this->assertEquals($user, $token->getUser());
    }

    public function testLoadAccessTokenBy()
    {
        $criteria = array('foo' => 'bar');

        $this->objectRepository->expects($this->once())
            ->method('findOneBy')
            ->with($this->equalTo($criteria))
            ->will($this->returnValue(array()));

        $this->tokenProvider->loadAccessTokenBy($criteria);
    }

    public function testLoadAccessTokenByToken()
    {
        $token = 'foo';

        $this->objectRepository->expects($this->once())
            ->method('findOneBy')
            ->with($this->equalTo(array('token' => $token)))
            ->will($this->returnValue(array()));

        $this->tokenProvider->loadAccessTokenByToken($token);
    }

    public function testLoadAccessTokens()
    {
        $this->objectRepository->expects($this->once())
            ->method('findAll')
            ->will($this->returnValue(array()));

        $this->tokenProvider->loadAccessTokens();
    }

    public function testCreateAccessToken()
    {
        $consumer = $this->getMock('Bazinga\OAuthServerBundle\Model\ConsumerInterface');
        $user = $this->getMock('Symfony\Component\Security\Core\User\UserInterface');

        $this->objectManager->expects($this->once())
            ->method('persist')
            ->with($this->isInstanceOf(static::ACCESS_TOKEN_CLASS));

        $this->objectManager->expects($this->once())
            ->method('flush');

        $token = $this->tokenProvider->createAccessToken($consumer, $user);

        $this->assertInstanceOf(static::ACCESS_TOKEN_CLASS, $token);
        $this->assertEquals($consumer, $token->getConsumer());
    }

    public function testDeleteAccessToken()
    {
        $token = new DummyAccessToken();

        $this->objectManager->expects($this->once())
            ->method('remove')
            ->with($this->equalTo($token));

        $this->objectManager->expects($this->once())
            ->method('flush');

        $this->tokenProvider->deleteAccessToken($token);
    }

    public function testDeleteToken()
    {
        $token = new DummyToken();

        $this->objectManager->expects($this->once())
            ->method('remove')
            ->with($this->equalTo($token));

        $this->objectManager->expects($this->once())
            ->method('flush');

        $this->tokenProvider->deleteToken($token);
    }

    public function testDeleteExpired()
    {
        $requestToken = $this->getMock(static::REQUEST_TOKEN_CLASS);

        $requestToken->expects($this->once())
            ->method('hasExpired')
            ->will($this->returnValue(true));

        $this->objectRepository->expects($this->at(0))
            ->method('findAll')
            ->will($this->returnValue(array($requestToken)));

        $accessToken = $this->getMock(static::ACCESS_TOKEN_CLASS);

        $accessToken->expects($this->once())
            ->method('hasExpired')
            ->will($this->returnValue(false));

        $this->objectRepository->expects($this->at(1))
            ->method('findAll')
            ->will($this->returnValue(array($accessToken)));

        $this->objectManager->expects($this->once())
            ->method('remove')
            ->with($this->isInstanceOf('Bazinga\OAuthServerBundle\Model\TokenInterface'));

        $this->tokenProvider->deleteExpired();
    }

    public function testUpdateToken()
    {
        $token = new DummyToken();

        $this->objectManager->expects($this->once())
            ->method('persist')
            ->with($this->equalTo($token));

        $this->objectManager->expects($this->once())
            ->method('flush');

        $this->tokenProvider->updateToken($token);
    }
}

class DummyToken extends Token
{

}

class DummyRequestToken extends RequestToken
{

}

class DummyAccessToken extends AccessToken
{

}
