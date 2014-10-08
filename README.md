nimic
=====

A console app backbone with a cacheable dependency injection container

```php
$kernel = new \Flo\Nimic\Kernel\Kernel;
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