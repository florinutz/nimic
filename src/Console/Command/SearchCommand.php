<?php
// florin, 10/7/14, 12:03 AM
namespace Flo\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Logger\ConsoleLogger;

class SearchCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('crawl:search')
            ->setDescription(
                'Perform a search'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $logger = new ConsoleLogger($output);
        $logger->alert('plm', ['caca' => 'maca']);
    }
} 