<?php

declare(strict_types=1);

namespace Omikron\FactFinder\Shopware6\Export\Upload;

use Shopware\Core\System\SystemConfig\SystemConfigService;

class Config
{
    /** @var SystemConfigService */
    private $systemConfig;

    public function __construct(SystemConfigService $systemConfig)
    {
        $this->systemConfig = $systemConfig;
    }

    public function getHost(): string
    {
        return (string) $this->config('ftpHost');
    }

    public function getPort(): string
    {
        return (string) $this->config('ftpPort') ?: '21';
    }

    public function getUserName(): string
    {
        return (string) $this->config('ftpUsername');
    }

    public function getPassword(): string
    {
        return (string) $this->config('ftpPassword');
    }

    public function getUploadFileName(): string
    {
        return (string) sprintf('export.%s.csv', $this->config('channel'));
    }

    private function config(string $param): string
    {
        return (string) $this->systemConfig->get('OmikronFactFinder.config.' . $param);
    }
}
