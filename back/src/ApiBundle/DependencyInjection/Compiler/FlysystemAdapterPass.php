<?php

namespace ApiBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class FlysystemAdapterPass implements CompilerPassInterface
{
    /**
     * You can modify the container here before it is dumped to PHP code.
     *
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->has('api.filesystem_adapter_manager')) {
            return;
        }

        $definition = $container->findDefinition('api.filesystem_adapter_manager');
        $taggedServices = $container->findTaggedServiceIds('api.filesystem_adapter');

        foreach ($taggedServices as $id => $tags) {
            foreach ($tags as $attributes) {
                $definition->addMethodCall('addFilesystemAdapter', [
                    new Reference($id),
                    $attributes['alias'],
                ]);
            }
        }
    }
}
