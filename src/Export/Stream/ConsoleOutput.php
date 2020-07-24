<?php

declare(strict_types=1);

namespace Omikron\FactFinder\Shopware6\Export\Stream;

use SplFileObject as File;
use Symfony\Component\Console\Output\OutputInterface;

class ConsoleOutput implements StreamInterface
{
    /** @var OutputInterface */
    private $output;

    /** @var string */
    private $delimiter;

    /** @var File */
    private $fileResource;

    public function __construct(OutputInterface $output, string $delimiter = ';')
    {
        $this->output    = $output;
        $this->delimiter = $delimiter;
    }

    public function addEntity(array $entity): void
    {
        $this->fileResource = $this->fileResource ?? new File('php://output', 'w');
        ob_start();
        $this->fileResource->fputcsv($entity, $this->delimiter);
        $this->output->writeln(rtrim(ob_get_clean()));
    }
}
