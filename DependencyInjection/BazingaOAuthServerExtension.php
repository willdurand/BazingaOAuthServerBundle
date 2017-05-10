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
        $configuration = new Configuration($container->getParameter('kernel.debug'));
        $config = $processor->processConfiguration($configuration, $configs);

        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.xml');

        $loader->load(sprintf('%s.xml', $config['mapping']['db_driver']));
        $container->setParameter('bazinga.oauth.backend_type_' . $config['mapping']['db_driver'], true);

        $container->setParameter('bazinga.oauth.model.consumer.class', $config['mapping']['consumer_class']);
        $container->setParameter('bazinga.oauth.model.request_token.class', $config['mapping']['request_token_class']);
        $container->setParameter('bazinga.oauth.model.access_token.class', $config['mapping']['access_token_class']);
        $container->setParameter('bazinga.oauth.model_manager_name', $config['mapping']['model_manager_name']);

        $serverServiceClass = '%bazinga.oauth.server_service.oauth.class%';
        if (isset($config['enable_xauth']) && true === $config['enable_xauth']) {
            $serverServiceClass = '%bazinga.oauth.server_service.xauth.class%';
        }

        $container
            ->getDefinition('bazinga.oauth.server_service')
            ->setClass($serverServiceClass)
            ->replaceArgument(2, new Reference($config['service']['nonce_provider']));
    }
}
