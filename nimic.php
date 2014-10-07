<?php
// florin, 10/6/14, 10:26 PM

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Flo\DependencyInjection\MainExtension;

use Flo\DependencyInjection\CommandCompilerPass;

$loader = require 'vendor/autoload.php';

$container = new ContainerBuilder();
$extension = new MainExtension();
$container->registerExtension($extension);
$container->loadFromExtension($extension->getAlias());
$container->addCompilerPass(new CommandCompilerPass());
$container->compile();

/** @var \Symfony\Component\Console\Application $app */
$app = $container->get('app');
$app->run();
