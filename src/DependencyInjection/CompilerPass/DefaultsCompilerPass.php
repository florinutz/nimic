<?php
// florin, 10/7/14, 12:11 AM
namespace Flo\Nimic\DependencyInjection\CompilerPass;

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
        $this->registerIfNeeded($container, 'app', 'Symfony\Component\Console\Application');
        $this->registerIfNeeded($container, 'dispatcher', 'Symfony\Component\EventDispatcher\EventDispatcher');
    }

    private function registerIfNeeded(ContainerBuilder $container, $id, $class, $arguments=[])
    {
        if (!$container->hasParameter("$id.class")) {
            $container->setParameter( "$id.class", $class );
        }
        if (!$container->has($id)) {
            $definition = new Definition("%$id.class%", $arguments);
            $container->setDefinition($id, $definition);
        }
    }
} 