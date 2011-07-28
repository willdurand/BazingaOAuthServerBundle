<?php

namespace Bazinga\OAuthBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;

use Bazinga\OAuthBundle\DependencyInjection\Compiler\AddSignaturesPass;

/**
 * BazingaOAuthBundle class.
 *
 * @package     BazingaOAuthBundle
 * @author William DURAND <william.durand1@gmail.com>
 */
class BazingaOAuthBundle extends Bundle
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
