<?php

namespace Bazinga\OAuthServerBundle\Tests\Command;

use Bazinga\OAuthServerBundle\Command\CleanCommand;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * @author Robin van der Vleuten <robinvdvleuten@gmail.com>
 */
class CleanCommandTest extends \PHPUnit_Framework_TestCase
{
    public function testExecute()
    {
        $tokenProvider = $this->getMock('Bazinga\OAuthServerBundle\Model\Provider\OAuthTokenProviderInterface');

        $tokenProvider->expects($this->atLeastOnce())
            ->method('deleteExpired')
            ->will($this->returnValue(10));

        $serverService = $this->getMock('Bazinga\OAuthServerBundle\Service\OAuthServerServiceInterface');

        $serverService->expects($this->atLeastOnce())
            ->method('getTokenProvider')
            ->will($this->returnValue($tokenProvider));

        $container = $this->getMock('Symfony\Component\DependencyInjection\ContainerInterface');

        $container->expects($this->atLeastOnce())
            ->method('get')
            ->with('bazinga.oauth.server_service')
            ->will($this->returnValue($serverService));

        $kernel = $this->getMock('Symfony\Component\HttpKernel\KernelInterface');

        $kernel->expects($this->atLeastOnce())
            ->method('getContainer')
            ->will($this->returnValue($container));

        $application = new Application($kernel);
        $application->add(new CleanCommand());

        $command = $application->find('bazinga:oauth-server:clean');
        $commandTester = new CommandTester($command);
        $commandTester->execute(array());

        $this->assertEquals("Removed 10 items from OAuth Server service.\n", $commandTester->getDisplay());
    }
}
