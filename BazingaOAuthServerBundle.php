<?php

namespace Bazinga\OAuthServerBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;

use Bazinga\OAuthServerBundle\DependencyInjection\BazingaOAuthServerExtension;
use Bazinga\OAuthServerBundle\DependencyInjection\Compiler\AddSignaturesPass;
use Bazinga\OAuthServerBundle\DependencyInjection\Security\Factory\OAuthFactory;

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
    }
}
