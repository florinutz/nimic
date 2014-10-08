Nimic
=====
A console app backbone with a cacheable dependency injection container

Installation
------------
You can clone the [Github repo][1] or require [flo/nimic][2] in your composer.json.

Basic usage
-----------
```php
$kernel = new \Flo\Nimic\Kernel\NimiKernel;
/**
 * This is a Symfony2 container
 */
$container = $kernel->getContainer();
/**
 * with some predefined services, like this Console Component application
 * on which you can add your commands:
 */
$app = $container->get('app');
$app->run();
```

Defining a new command
----------------------
```php
```

[1]: https://github.com/florinutz/nimic
[2]: https://packagist.org/packages/flo/nimic