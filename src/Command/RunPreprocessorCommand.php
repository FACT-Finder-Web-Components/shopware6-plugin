<?php

declare(strict_types=1);

namespace Omikron\FactFinder\Shopware6\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.ElseExpression)
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
class RunPreprocessorCommand extends Command
{
    public const SALES_CHANNEL_ARGUMENT          = 'sales_channel';
    public const SALES_CHANNEL_LANGUAGE_ARGUMENT = 'language';

    public function __construct()
    {
        parent::__construct();
    }

    public function configure(): void
    {
        $this->setName('factfinder:data:pre-process');
        $this->setDescription('Run the Feed preprocessor');
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('Cache export is not support in SDK version 5.x');

        return 0;
    }
}
