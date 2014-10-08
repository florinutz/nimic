<?php
// florin, 10/7/14, 9:52 PM
namespace Flo\Nimic\Kernel;

use Symfony\Component\Config\ConfigCache;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Flo\Nimic\DependencyInjection\Extension\MainExtension;
use Symfony\Component\DependencyInjection\Dumper\PhpDumper;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Flo\Nimic\DependencyInjection\CompilerPass\DefaultsCompilerPass;
use Flo\Nimic\DependencyInjection\CompilerPass\CommandCompilerPass;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class Kernel
{
    /** @var ContainerBuilder */
    protected $container;

    /** @var bool */
    protected $debug;

    /** @var EventDispatcherInterface */
    protected $dispatcher;

    function __construct($debug)
    {
        $this->setDebug($debug);
        $this->container = $this->getContainer();
        $this->dispatcher = new EventDispatcher();
    }

    /**
     * @return ContainerBuilder
     */
    public function getContainer()
    {
        if ($this->container) {
            return $this->container;
        }
        if ($this->getCacheDir()) {
            $containerBuilder = $this->buildContainerWithCache($this->getCacheDir() . '/container.php', 'CachedContainer');
        }
        else {
            $containerBuilder = $this->buildContainer();
        }
        $containerBuilder->set('kernel', $this);
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

    /**
     * @return ContainerBuilder
     */
    protected function buildContainer()
    {
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
        return $containerBuilder;
    }

    /**
     * @param $cacheFile
     * @param $cachedContainerClassName
     * @return ContainerBuilder
     */
    protected function buildContainerWithCache($cacheFile, $cachedContainerClassName)
    {
        $containerConfigCache = new ConfigCache($cacheFile, $this->isDebug());
        $cachedContainerClassName = '\\' . $cachedContainerClassName;
        if (! $containerConfigCache->isFresh()) {
            $containerBuilder = $this->buildContainer();
            $dumper = new PhpDumper($containerBuilder);
            $containerConfigCache->write($dumper->dump(['class' => $cachedContainerClassName]), $containerBuilder->getResources());
            return $containerBuilder;
        }
        else {
            require_once $cacheFile;
            $containerBuilder = new $cachedContainerClassName;
            return $containerBuilder;
        }
    }

} 