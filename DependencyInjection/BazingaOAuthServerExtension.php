<?php

namespace Bazinga\OAuthServerBundle\DependencyInjection;

use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Config\FileLocator;

/**
 * @author William DURAND <william.durand1@gmail.com>
 */
class BazingaOAuthServerExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function getAlias()
    {
        return 'bazinga_oauth_server';
    }

    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $processor = new Processor();
        $configuration = new Configuration($container->get('kernel.debug'));
        $config = $processor->processConfiguration($configuration, $configs);

        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.xml');

        $serverServiceClass = '%bazinga.oauth.server_service.oauth.class%';
        if (isset($config['enable_xauth']) && true === $config['enable_xauth']) {
            $serverServiceClass = '%bazinga.oauth.server_service.xauth.class%';
        }

        $container->getDefinition('bazinga.oauth.server_service')->setClass($serverServiceClass);

        if (isset($config['service']) &&
            isset($config['service']['consumer_provider']) &&
            isset($config['service']['token_provider']) &&
            isset($config['service']['nonce_provider'])
        ) {
            $container
                ->getDefinition('bazinga.oauth.server_service')
                ->replaceArgument(0, new Reference($config['service']['consumer_provider']))
                ->replaceArgument(1, new Reference($config['service']['token_provider']))
                ->replaceArgument(2, new Reference($config['service']['nonce_provider']));

            $container->getDefinition('bazinga.oauth.controller.server')
                ->replaceArgument(3, new Reference($config['service']['token_provider']));
            $container->getDefinition('bazinga.oauth.controller.login')
                ->replaceArgument(2, new Reference($config['service']['token_provider']));
        } else {
            throw new \RuntimeException('Services "consumer_provider" and "token_provider" have to be defined.');
        }
    }
}
