<?php

namespace Bazinga\OAuthServerBundle\DependencyInjection\Security\Factory;

use Symfony\Bundle\SecurityBundle\DependencyInjection\Security\Factory\SecurityFactoryInterface;
use Symfony\Component\Config\Definition\Builder\NodeDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\DefinitionDecorator;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @author William DURAND <william.durand1@gmail.com>
 */
class OAuthFactory implements SecurityFactoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function create(ContainerBuilder $container, $id, $config, $userProvider, $defaultEntryPoint)
    {
        $providerId = 'security.authentication.provider.bazinga_oauth.'.$id;
        $container
            ->setDefinition($providerId, new DefinitionDecorator('bazinga.oauth.security.authentication.provider'))
            ->replaceArgument(0, new Reference($userProvider))
            ;

        $listenerId = 'security.authentication.listener.bazinga_oauth.'.$id;
        $listener   = $container->setDefinition($listenerId, new DefinitionDecorator('bazinga.oauth.security.authentication.listener'));

        return array($providerId, $listenerId, $defaultEntryPoint);
    }

    /**
     * {@inheritdoc}
     */
    public function getPosition()
    {
        return 'pre_auth';
    }

    /**
     * {@inheritdoc}
     */
    public function getKey()
    {
        return 'bazinga_oauth';
    }

    /**
     * {@inheritdoc}
     */
    public function addConfiguration(NodeDefinition $node)
    {
    }
}
