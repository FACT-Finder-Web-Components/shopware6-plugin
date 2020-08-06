<?php

declare(strict_types=1);

namespace Omikron\FactFinder\Shopware6\Config;

use Shopware\Core\System\SystemConfig\SystemConfigService;

abstract class BaseConfig
{
    /** @var SystemConfigService */
    private $systemConfig;

    public function __construct(SystemConfigService $systemConfig)
    {
        $this->systemConfig = $systemConfig;
    }

    /**
     * @param string $param
     *
     * @return mixed
     */
    protected function config(string $param)
    {
        return $this->systemConfig->get('OmikronFactFinder.config.' . $param);
    }
}
