<?php

declare(strict_types=1);

namespace Omikron\FactFinder\Shopware6\Config;

use Shopware\Core\System\SystemConfig\SystemConfigService;

abstract class BaseConfig
{
    public function __construct(private readonly SystemConfigService $systemConfig)
    {
    }

    /**
     * @param string      $param
     * @param string|null $salesChannelId
     *
     * @return mixed
     */
    protected function config(string $param, ?string $salesChannelId = null)
    {
        return $this->systemConfig->get('OmikronFactFinder.config.' . $param, $salesChannelId);
    }
}
