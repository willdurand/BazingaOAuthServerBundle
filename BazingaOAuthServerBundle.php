<?php

namespace Bazinga\OAuthServerBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;

use Bazinga\OAuthServerBundle\DependencyInjection\Compiler\AddSignaturesPass;

/**
 * BazingaOAuthServerBundle class.
 *
 * @package     BazingaOAuthServerBundle
 * @author William DURAND <william.durand1@gmail.com>
 */
class BazingaOAuthServerBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new AddSignaturesPass());
    }
}
