<?php
// florin, 10/7/14, 12:11 AM
namespace Flo\Nimic\DependencyInjection\CompilerPass;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;

class CommandCompilerPass implements CompilerPassInterface
{
    /** @var string */
    protected $commandTag;

    /**
     * @param string $commandTag Tag name used for identifying commands
     */
    function __construct($commandTag = 'command')
    {
        $this->commandTag = $commandTag;
    }

    public function process(ContainerBuilder $container)
    {
        if ($container->hasDefinition('app')) {
            $definition = $container->getDefinition('app');
            $taggedServices = $container->findTaggedServiceIds('command');
            foreach ($taggedServices as $id => $attributes) {
                $definition->addMethodCall('add', [new Reference($id)]);
                $commandDefinition = $container->getDefinition($id);
                if ($container->hasDefinition('service_container') && $this->classIsContainerAware($commandDefinition->getClass())) {
                    $commandDefinition->addMethodCall('setContainer', [new Reference('service_container')]);
                }
            }
        }
    }

    private function classIsContainerAware($class)
    {
        if (!class_exists($class)) {
            return false;
        }
        $class = new \ReflectionClass($class);
        return $class->implementsInterface('Symfony\Component\DependencyInjection\ContainerAwareInterface');
    }
}