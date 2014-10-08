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
You basically have to [create your container extension][5], [create your new command class][3] tagged as'command' and then [add its definition][4] to the container through the extension.

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
[1]: https://github.com/florinutz/nimic
[2]: https://packagist.org/packages/flo/nimic
[3]: http://symfony.com/doc/current/components/console/introduction.html#creating-a-basic-command
[4]: http://symfony.com/doc/current/components/dependency_injection/definitions.html
[5]: http://symfony.com/doc/current/components/dependency_injection/compilation.html#managing-configuration-with-extensions