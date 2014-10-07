<?php
// florin, 10/6/14, 10:26 PM
use Flo\Kernel\Kernel;
$loader = require 'vendor/autoload.php';
$kernel = new Kernel(true);
$app = $kernel->getContainer()->get('app');
$app->run();
