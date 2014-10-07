<?php
// florin, 10/7/14, 12:11 AM
namespace Flo\DependencyInjection\CompilerPass;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

class DefaultsCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $serviceContainerDefinition = new Definition();
        $serviceContainerDefinition->setSynthetic(true);
        $container->setDefinition('service_container', $serviceContainerDefinition);
        $container->setDefinition('kernel', $serviceContainerDefinition);
    }
} 