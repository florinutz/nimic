<?php
// florin, 10/6/14, 10:26 PM

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

use Flo\DependencyInjection\CommandCompilerPass;

$loader = require 'vendor/autoload.php';

$container = new ContainerBuilder();

$loader = new XmlFileLoader($container, new FileLocator(__DIR__));
$loader->load('services.xml');

$container->addCompilerPass(new CommandCompilerPass());

$app = $container->get('app');
$app->run();
