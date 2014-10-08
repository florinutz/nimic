# Nimic
A console app backbone with a cacheable dependency injection container

## Installation
You can clone the [Github repo][1] or require [flo/nimic][2] in your composer.json.

## Basic usage
Inside your app put this in somefile.php

```php
/**
 * If you use composer you should use its autoloader:
 */
require 'vendor/autoload.php';

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

## Adding a new command
You basically have to 

1. [create your extension][5] class
2. [create your new command class][3] tagged **_command_** 
3. using the extension, [add the command service definition][4] to the container

In order to register your extension with the container, you'll have to override NimiKernel::getExtensions() (and use your Kernel from now on). 
This method should return an array of ExtensionInterface instances.

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
Using the extension, you can add any service to the container, not just Command classes.

## example.php
```php
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
```
[1]: https://github.com/florinutz/nimic
[2]: https://packagist.org/packages/flo/nimic
[3]: http://symfony.com/doc/current/components/console/introduction.html#creating-a-basic-command
[4]: http://symfony.com/doc/current/components/dependency_injection/definitions.html
[5]: http://symfony.com/doc/current/components/dependency_injection/compilation.html#managing-configuration-with-extensions
