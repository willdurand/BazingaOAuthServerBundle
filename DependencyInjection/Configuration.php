<?php

namespace Bazinga\OAuthServerBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * @author William DURAND <william.durand1@gmail.com>
 */
class Configuration implements ConfigurationInterface
{
    /**
     * Generates the configuration tree builder.
     *
     * @return TreeBuilder The tree builder
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('bazinga_oauth_server');

        $this->addDefaultSection($rootNode);
        $this->addMappingSection($rootNode);
        $this->addServiceSection($rootNode);

        return $treeBuilder;
    }

    private function addDefaultSection(ArrayNodeDefinition $node)
    {
        $node
            ->children()
                ->booleanNode('enable_xauth')->defaultValue(false)->end()
            ->end();
    }

    private function addMappingSection(ArrayNodeDefinition $node)
    {
        $supportedDrivers = array('orm', 'mongodb', 'couchdb', 'propel');

        $node
            ->children()
                ->arrayNode('mapping')
                    ->isRequired()
                    ->children()
                        ->scalarNode('db_driver')
                            ->validate()
                                ->ifNotInArray($supportedDrivers)
                                ->thenInvalid('The driver %s is not supported. Please choose one of '.json_encode($supportedDrivers))
                            ->end()
                            ->cannotBeOverwritten()
                            ->isRequired()
                            ->cannotBeEmpty()
                        ->end()
                        ->scalarNode('consumer_class')->isRequired()->cannotBeEmpty()->end()
                        ->scalarNode('request_token_class')->isRequired()->cannotBeEmpty()->end()
                        ->scalarNode('access_token_class')->isRequired()->cannotBeEmpty()->end()
                        ->scalarNode('model_manager_name')->defaultNull()->end()
                    ->end()
                ->end()
            ->end();
    }

    private function addServiceSection(ArrayNodeDefinition $node)
    {
        $node
            ->children()
                ->arrayNode('service')
                    ->isRequired()
                    ->children()
                        ->scalarNode('nonce_provider')->isRequired()->cannotBeEmpty()->end()
                    ->end()
                ->end()
            ->end();
    }
}
