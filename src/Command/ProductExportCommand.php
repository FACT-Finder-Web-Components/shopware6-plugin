<?php

declare(strict_types=1);

namespace Omikron\FactFinder\Shopware6\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ProductExportCommand extends Command
{
    public function __construct()
    {
        parent::__construct('factfinder:export:products');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('<info>Not implemented yet</info>');
    }
}
