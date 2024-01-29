<?php

declare(strict_types=1);

namespace Omikron\FactFinder\Shopware6\Config;

use Shopware\Core\System\SystemConfig\SystemConfigService;

abstract class BaseConfig
{
    private SystemConfigService $systemConfig;

    public function __construct(SystemConfigService $systemConfig)
    {
        $this->systemConfig = $systemConfig;
    }

    /**
     * @param string      $param
     * @param string|null $salesChannelId
     *
     * @return mixed
     */
    protected function config(string $param, ?string $salesChannelId = null)
    {
        return $this->systemConfig->get('FactFinder.config.' . $param, $salesChannelId);
    }
}
