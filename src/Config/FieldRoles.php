<?php

declare(strict_types=1);

namespace Omikron\FactFinder\Shopware6\Config;

use Omikron\FactFinder\Communication\Resource\Search;
use Shopware\Core\System\SystemConfig\SystemConfigService;
use function Omikron\FactFinder\Shopware6\Internal\Utils\safeGetByName;

class FieldRoles implements FieldRolesInterface
{
    private Search $search;
    private Communication $communicationConfig;
    private SystemConfigService $systemConfig;

    public function __construct(Search $search, Communication $communication, SystemConfigService $systemConfig)
    {
        $this->search              = $search;
        $this->communicationConfig = $communication;
        $this->systemConfig        = $systemConfig;
    }

    public function getRoles(?string $salesChannelId): array
    {
        try {
            $searchResult = $this->search->search($this->communicationConfig->getChannel($salesChannelId), '*');
            $fieldRoles   = $searchResult['fieldRoles'] ?? [];
        } catch (\Exception $e) {
            $fieldRoles = [];
        }

        return $this->map($fieldRoles);
    }

    public function update(array $fieldRoles, ?string $salesChannelId): void
    {
        $this->systemConfig->set('OmikronFactFinder.config.fieldRoles', $fieldRoles, $salesChannelId);
    }

    private function map(array $fieldRoles): array
    {
        $getRole = fn (string $key) => safeGetByName($fieldRoles, $key);

        return [
            'brand'                 => $getRole('brand'),
            'campaignProductNumber' => $getRole('productNumber'),
            'deeplink'              => $getRole('deeplink'),
            'description'           => $getRole('description'),
            'displayProductNumber'  => $getRole('productNumber'),
            'ean'                   => $getRole('ean'),
            'imageUrl'              => $getRole('imageUrl'),
            'masterArticleNumber'   => $getRole('masterId'),
            'price'                 => $getRole('price'),
            'productName'           => $getRole('productName'),
            'trackingProductNumber' => $getRole('productNumber'),
        ];
    }
}
