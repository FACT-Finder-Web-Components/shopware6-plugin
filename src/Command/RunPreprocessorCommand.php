<?php

declare(strict_types=1);

namespace Omikron\FactFinder\Shopware6\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.ElseExpression)
 */
class RunPreprocessorCommand extends Command
{
    public function configure(): void
    {
        $this->setName('factfinder:data:pre-process');
        $this->setDescription('Run the Feed preprocessor');
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('Cache export is not support in SDK version 6.x');

        return 0;
    }
}
