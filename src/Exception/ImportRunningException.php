<?php

declare(strict_types=1);

namespace Omikron\FactFinder\Shopware6\Exception;

use \RuntimeException;

class ImportRunningException extends RuntimeException
{
    public function __construct()
    {
        parent::__construct('Push import is currently running. Please make sure that import process is finished before starting new one.');
    }
}
