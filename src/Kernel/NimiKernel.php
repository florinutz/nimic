<?php
// florin, 10/7/14, 9:52 PM
namespace Flo\Nimic\Kernel;

use Flo\Nimic\Console\Application;
use Monolog\Logger;
use Symfony\Component\Config\ConfigCache;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Flo\Nimic\DependencyInjection\Extension\MainExtension;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Dumper\PhpDumper;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\EventDispatcher\DependencyInjection\RegisterListenersPass;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\Debug\Debug;
use Flo\Nimic\DependencyInjection\CompilerPass\CommandCompilerPass;

class NimiKernel implements ContainerAwareInterface
{
    /** @var ContainerBuilder */
    protected $container;

    /** @var bool */
    protected $debug;

    /** @var string */
    protected $cacheDir;

    /** @var string */
    protected $name = 'Nimic';

    /** @var string */
    protected $version = "0.1";

    function __construct($debug=false, $cacheDir=null)
    {
        if ($debug) {
            Debug::enable();
        }
        $this->setDebug($debug);
        $this->setContainer($this->getContainer());
    }

    /**
     * This should load any config files it's loaded after extensions
     * so it should contain extension alias root keys
     *
     * @param ContainerBuilder $container
     * @return bool
     */
    protected function loadConfigResourcesIntoContainer(ContainerBuilder $container)
    {
        return false;
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
        $container->addCompilerPass(new CommandCompilerPass('app', 'command'));
        $container->addCompilerPass(new RegisterListenersPass('dispatcher', 'listener', 'subscriber'));
    }

    /**
     * @return string
     */
    public function getCacheDir()
    {
        return $this->cacheDir;
    }

    /**
     * @param string $cacheDir
     */
    public function setCacheDir($cacheDir)
    {
        $this->cacheDir = $cacheDir;
    }

    /**
     * @return array Array of your own compiler passes
     */
    protected function getCompilerPasses()
    {
        return [];
    }

    /**
     * @return array Array of your own extensions
     */
    protected function getExtensions()
    {
        return [ new MainExtension() ];
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
        $container->setParameter('cache_dir', $this->getCacheDir());
        $container->setParameter('debug', $this->isDebug());
        $container->setParameter('app.name', $this->getName());
        $container->setParameter('app.version', $this->getVersion());
    }

    /**
     * @return ContainerBuilder
     */
    protected function buildContainer()
    {
        $containerBuilder = new ContainerBuilder();
        $this->addKernelParametersToContainer($containerBuilder);
        foreach ($this->getExtensions() as $extension) {
            /** @var ExtensionInterface $extension */
            $containerBuilder->registerExtension($extension);
            $containerBuilder->loadFromExtension($extension->getAlias());
        }
        $this->loadConfigResourcesIntoContainer($containerBuilder);
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
        if (! $containerConfigCache->isFresh()) {
            $containerBuilder = $this->buildContainer();
            $dumper = new PhpDumper($containerBuilder);
            $containerConfigCache->write($dumper->dump(['class' => $cachedContainerClassName]), $containerBuilder->getResources());
            return $containerBuilder;
        }
        else {
            require_once $cacheFile;
            $cachedContainerClassName = '\\' . $cachedContainerClassName;
            $containerBuilder = new $cachedContainerClassName;
            return $containerBuilder;
        }
    }

    /**
     * Sets the Container.
     *
     * @param ContainerInterface|null $container A ContainerInterface instance or null
     * @api
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
        $this->getContainer()->setParameter('app.name', $this->getName());
    }

    /**
     * @return string
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * @param string $version
     */
    public function setVersion($version)
    {
        $this->version = $version;
        $this->getContainer()->setParameter('app.version', $this->getVersion());
    }


    /**
     * Shortcuts
     */

    /**
     * @return EventDispatcher
     */
    public function getEventDispatcher()
    {
        return $this->container->get('dispatcher');
    }

    /**
     * @return Application
     */
    public function getApplication()
    {
        return $this->container->get('app');
    }

    /**
     * @return Logger
     */
    public function getLogger()
    {
        return $this->container->get('logger');
    }


}