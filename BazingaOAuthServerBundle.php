<?php

namespace Bazinga\OAuthServerBundle;

use Bazinga\OAuthServerBundle\DependencyInjection\BazingaOAuthServerExtension;
use Bazinga\OAuthServerBundle\DependencyInjection\Compiler\AddSignaturesPass;
use Bazinga\OAuthServerBundle\DependencyInjection\Compiler\RegisterMappingsPass;
use Bazinga\OAuthServerBundle\DependencyInjection\Security\Factory\OAuthFactory;
use Doctrine\Bundle\CouchDBBundle\DependencyInjection\Compiler\DoctrineCouchDBMappingsPass;
use Doctrine\Bundle\DoctrineBundle\DependencyInjection\Compiler\DoctrineOrmMappingsPass;
use Doctrine\Bundle\MongoDBBundle\DependencyInjection\Compiler\DoctrineMongoDBMappingsPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * @author William DURAND <william.durand1@gmail.com>
 */
class BazingaOAuthServerBundle extends Bundle
{
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->extension = new BazingaOAuthServerExtension();
    }

    /**
     * {@inheritDoc}
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new AddSignaturesPass());

        $extension = $container->getExtension('security');
        $extension->addSecurityListenerFactory(new OAuthFactory());

        $this->addRegisterMappingsPass($container);
    }

    private function addRegisterMappingsPass(ContainerBuilder $container)
    {
        $mappings = array(
            realpath(__DIR__ . '/Resources/config/model') => 'Bazinga\OAuthServerBundle\Model',
        );

        if (class_exists('Doctrine\Bundle\DoctrineBundle\DependencyInjection\Compiler\DoctrineOrmMappingsPass')) {
            $container->addCompilerPass(DoctrineOrmMappingsPass::createXmlMappingDriver($mappings, array('bazinga.oauth.model_manager_name'), 'bazinga.oauth.backend_type_orm'));
        }

        if (class_exists('Doctrine\Bundle\MongoDBBundle\DependencyInjection\Compiler\DoctrineMongoDBMappingsPass')) {
            $container->addCompilerPass(DoctrineMongoDBMappingsPass::createXmlMappingDriver($mappings, array('bazinga.oauth.model_manager_name'), 'bazinga.oauth.backend_type_mongodb'));
        }

        if (class_exists('Doctrine\Bundle\CouchDBBundle\DependencyInjection\Compiler\DoctrineCouchDBMappingsPass')) {
            $container->addCompilerPass(DoctrineCouchDBMappingsPass::createXmlMappingDriver($mappings, array('bazinga.oauth.model_manager_name'), 'bazinga.oauth.backend_type_couchdb'));
        }
    }
}
