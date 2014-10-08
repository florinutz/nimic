<?php
/**
 * If you use composer you should use its autoloader:
 */
$loader = require 'vendor/autoload.php';

/**
 * In case you need to inject custom services into the container you'll have to:
 * override NimiKernel
 * create an extension
 * add its instance to the array returner by YourCustomKernel::getExtensions (so that the extension will be registered before the container gets compiled)
 * make the extension load your services xml or yaml or whatever
 */
$kernel = new Flo\Nimic\Kernel\NimiKernel;

/**
 * If the kernel returns a writable cache dir ( YourCustomKernel::getCacheDir ) then the container is cached
 */
$container = $kernel->getContainer();

/**
 * This is the main entry point for your console application
 * http://symfony.com/doc/current/components/console/index.html
 */
$app = $container->get('app');

/**
 * Here you can add commands on the fly
 * The other method of adding commands is through the custom extension, by defining command services tagges "command"
 */

$app->run();
