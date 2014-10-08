<?php
// florin, 10/6/14, 10:26 PM
$loader = require 'vendor/autoload.php';
$kernel = new Flo\Nimic\Kernel\NimiKernel(true);
$container = $kernel->getContainer();
$app = $container->get('app');
$logger = $container->get('logger');
$app->run();
