<?php

declare(strict_types=1);

namespace Omikron\FactFinder\Shopware6\Communication;

use Omikron\FactFinder\Communication\Client\ClientBuilder;
use Omikron\FactFinder\Communication\Resource\AdapterFactory as BaseAdapterFactory;
use Omikron\FactFinder\Shopware6\Config\Communication;

class AdapterFactory extends BaseAdapterFactory
{
    public function __construct(
        ClientBuilder $clientBuilder,
        Communication $config
    ) {
        parent::__construct(
            $clientBuilder,
            $config->getVersion(),
            $config->getApiVersion()
        );
    }
}
