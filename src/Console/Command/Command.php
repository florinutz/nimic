<?php
// florin, 10/8/14, 9:46 PM


namespace Flo\Nimic\Console\Command;

use Symfony\Component\Console\Command\Command as SymfonyCommnand;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;

class Command extends SymfonyCommnand implements ContainerAwareInterface
{
    /** @var ContainerBuilder */
    protected $container;

    /**
     * Sets the Container.
     *
     * @param ContainerInterface|null $container A ContainerInterface instance or null
     * @api
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * @return ContainerBuilder
     */
    public function getContainer()
    {
        return $this->container;
    }

} 