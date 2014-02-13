<?php

namespace Bazinga\OAuthServerBundle\Command;

use Bazinga\OAuthServerBundle\Service\OAuthServerService;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @author Robin van der Vleuten <robinvdvleuten@gmail.com>
 */
class CleanCommand extends ContainerAwareCommand
{
    /**
     * {@inheritDoc}
     */
    protected function configure()
    {
        $this
            ->setName('bazinga:oauth-server:clean')
            ->setDescription('Clean expired tokens')
            ->setHelp(<<<EOT
The <info>%command.name%</info> command will remove expired OAuth tokens.

  <info>php %command.full_name%</info>
EOT
            );
    }

    /**
     * {@inheritDoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var \Bazinga\OAuthServerBundle\Model\Provider\TokenProviderInterface $provider */
        $provider = $this->getContainer()->get('bazinga.oauth.server_service')->getTokenProvider();

        $result = $provider->deleteExpired();
        $output->writeln(sprintf(
            'Removed <info>%d</info> items from <comment>OAuth Server</comment> service.',
            $result
        ));
    }
}
