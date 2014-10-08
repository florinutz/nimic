<?php
// florin, 10/7/14, 12:11 AM
namespace Flo\Nimic\DependencyInjection\CompilerPass;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;

class CommandCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if ($container->hasDefinition('app')) {
            $definition = $container->getDefinition('app');
            $taggedServices = $container->findTaggedServiceIds('command');
            foreach ($taggedServices as $id => $attributes) {
                $definition->addMethodCall('add', array(new Reference($id)));
                $commandDefinition = $container->getDefinition($id);
                if ($container->hasDefinition('service_container')) {
                    $commandDefinition->addMethodCall('setContainer', array(new Reference('service_container')));
                }
            }
        }
    }
} 