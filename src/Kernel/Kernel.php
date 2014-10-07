<?php
// florin, 10/7/14, 9:52 PM
namespace Flo\Kernel;

use Symfony\Component\Config\ConfigCache;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Flo\DependencyInjection\Extension\MainExtension;
use Symfony\Component\DependencyInjection\Dumper\PhpDumper;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Flo\DependencyInjection\CompilerPass\DefaultsCompilerPass;
use Flo\DependencyInjection\CompilerPass\CommandCompilerPass;

class Kernel 
{
    /** @var ContainerBuilder */
    protected $container;

    /** @var bool */
    protected $debug;

    function __construct($debug)
    {
        $this->setDebug($debug);
        $this->container = $this->getContainer();
    }

    /**
     * @return ContainerBuilder
     */
    public function getContainer()
    {
        if ($this->container) {
            return $this->container;
        }
        $cacheFile = $this->getCacheDir() . '/container.php';
        $containerConfigCache = new ConfigCache($cacheFile, $this->isDebug());
        if (!$containerConfigCache->isFresh()) {
            $containerBuilder = new ContainerBuilder();
            $this->addKernelParametersToContainer($containerBuilder);
            $this->addMainExtension($containerBuilder);
            foreach ($this->getExtensions() as $extension) {
                /** @var ExtensionInterface $extension */
                $containerBuilder->registerExtension($extension);
                $containerBuilder->loadFromExtension($extension->getAlias());
            }
            $this->addDefaultCompilerPasses($containerBuilder);
            foreach ($this->getCompilerPasses() as $compilerPass) {
                /** @var CompilerPassInterface $compilerPass */
                $containerBuilder->addCompilerPass($compilerPass);
            }
            // synthetic service
            $containerBuilder->set('service_container', $containerBuilder);
            $containerBuilder->compile();
            $dumper = new PhpDumper($containerBuilder);
            $containerConfigCache->write(
                $dumper->dump(['class' => 'CachedContainer']),
                $containerBuilder->getResources()
            );
        }
        else {
            require_once $cacheFile;
            $containerBuilder = new \CachedContainer;
        }
        return $this->container = $containerBuilder;
    }

    private function addDefaultCompilerPasses(ContainerBuilder $container)
    {
        $container->addCompilerPass(new DefaultsCompilerPass());
        $container->addCompilerPass(new CommandCompilerPass());
    }

    /**
     * @return string
     */
    protected function getRootDir()
    {
        return realpath(__DIR__ . '/../..');
    }

    protected function getCacheDir()
    {
        return $this->getRootDir() . '/cache';
    }

    /**
     * @return array Array of your own compiler passes
     */
    protected function getCompilerPasses()
    {
        return [];
    }

    /**
     * @param ContainerBuilder $container
     */
    private function addMainExtension(ContainerBuilder $container)
    {
        $extension = new MainExtension();
        $container->registerExtension($extension);
        $container->loadFromExtension($extension->getAlias());
    }

    /**
     * @return array Array of your own extensions
     */
    protected function getExtensions()
    {
        return [];
    }

    /**
     * @return boolean
     */
    public function isDebug()
    {
        return $this->debug;
    }

    /**
     * @param boolean $debug
     */
    public function setDebug($debug)
    {
        $this->debug = $debug;
    }

    /**
     * @param ContainerBuilder $container
     */
    protected function addKernelParametersToContainer($container)
    {
        $container->setParameter('root_dir', $this->getRootDir());
        $container->setParameter('cache_dir', $this->getCacheDir());
        $container->setParameter('debug', $this->isDebug());
    }

} 