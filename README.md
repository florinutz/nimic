# Nimic

Nimic provides a good starting point for a php console application. Its purpose is to provide a base for your code with access to a (cacheable) dependency injection container, a console application ready to carry (symfony) commands, an event dispatcher and a monolog instance and phpunit. It's basically a facade built upon symfony components.

## Installation
You can clone the [github repo][1], but the recommended method is through composer. Require [flo / nimic][2] in your composer.json.

## Basic usage
Inside your app put this in somefile.php

```php
/**
 * composer  autoloader:
 */
require 'vendor/autoload.php';

$kernel = new \Flo\Nimic\Kernel\NimiKernel; //extend this kernel!

/**
 * This is a Symfony2 container
 */
$container = $kernel->getContainer();

/**
 * with some predefined services, like this (Console Component) application 
 */
$app = $container->get('app');

/**
 * on which you should add your own commands: 
 */
$app->add(new MyCommand);

/**
 * before running it
 */
$app->run();
```
Create your commands [like this][3].

## Adding a new service
You have to 

1. [create your extension][5] class
2. using the extension, [add the command service definition][4] to the container.
3. (optional) If the service is a command, an event listener or subscriber then you should tag it with **command**, **listener** or **subscriber**. See [this][7] for events.

In order to register your extension with the container, you'll have to [override][8] NimiKernel::getExtensions(). 
This method should return an array of your ExtensionInterface instances, and it's quite possible that you'll need only one extension.

```php
class YourCustomKernel extends \Flo\Nimic\Kernel\NimiKernel
{
    ...
    /**
     * @return array Array of your own extensions
     */
    protected function getExtensions()
    {
        return [
            new YourExtension()
        ];
    }
    ...
}
```
And then continue with the basic usage example, but instead of 
```php
$kernel = new \Flo\Nimic\Kernel\NimiKernel;
```
do
```php
$kernel = new YourCustomKernel;
```
Again, using the extension, you can add (or override) any container service, not just Command classes.

## example.php
```php
#!/usr/bin/env php
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
 * Here you can add commands using $app->add()
 * The other method of adding commands is through the custom extension, by defining command services tagges "command"
 */

$app->run();
```

## Testing Commands
See [this][6].
[1]: https://github.com/florinutz/nimic
[2]: https://packagist.org/packages/flo/nimic
[3]: http://symfony.com/doc/current/components/console/introduction.html#creating-a-basic-command
[4]: http://symfony.com/doc/current/components/dependency_injection/definitions.html
[5]: http://symfony.com/doc/current/components/dependency_injection/compilation.html#managing-configuration-with-extensions
[6]: http://symfony.com/doc/current/components/console/introduction.html#testing-commands
[7]: http://symfony.com/doc/current/cookbook/service_container/event_listener.html
[8]: http://stackoverflow.com/questions/2994758/what-is-function-overloading-and-overriding-in-php
