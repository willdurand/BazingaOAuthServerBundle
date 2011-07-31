<?php

namespace Bazinga\OAuthServerBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * AddSignaturesPass class.
 *
 * @package     BazingaOAuthServerBundle
 * @subpackage  DependencyInjection
 * @author William DURAND <william.durand1@gmail.com>
 */
class AddSignaturesPass implements CompilerPassInterface
{
    /**
     * @var \Symfony\Component\DependencyInjection\ContainerBuilder
     */
    protected $container;

    /**
     * Get all Signature services based on their tag ('oauth.signature_service') and register them
     * to the OAuthServerService.
     *
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container    The container.
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('bazinga.oauth.controller.server')) {
            return;
        }

        // get the server service id
        $id = $container->getDefinition('bazinga.oauth.controller.server')
            ->getArgument(3);

        $this->container = $container;

        foreach ($container->findTaggedServiceIds('oauth.signature_service') as $signatureServiceId => $attributes) {
            $this->registerSignatureService($id, $signatureServiceId);
        }
    }

    /**
     * Register a SignatureService in the OAuthServerService.
     *
     * @param Reference $id                 The server service id.
     * @param string $signatureServiceId    The service identifier.
     */
    protected function registerSignatureService($id, $signatureServiceId)
    {
        $this->container
            ->getDefinition($id)
            ->addMethodCall('addSignatureService', array(new Reference($signatureServiceId)));
    }
}
