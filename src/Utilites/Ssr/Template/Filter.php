<?php

declare(strict_types=1);

namespace Omikron\FactFinder\Shopware6\Utilites\Ssr\Template;

use Omikron\FactFinder\Shopware6\Config\CachedFieldRoles;
use Omikron\FactFinder\Shopware6\Export\Filter\FilterInterface;
use Omikron\FactFinder\Shopware6\Export\SalesChannelService;

class Filter implements FilterInterface
{
    private array $fieldRoles;

    public function __construct(
        SalesChannelService $channelService,
        CachedFieldRoles $fieldRolesService
    ) {
        $context          = $channelService->getSalesChannelContext();
        $this->fieldRoles = $fieldRolesService->getRoles($context->getSalesChannelId());
    }

    public function filterValue(string $value): string
    {
        $value = preg_replace('#data-anchor="([^"]+?)"#', 'href="$1" $0', $value);
        $value = preg_replace('#data-redirect-target="_(blank|self|parent|top)"#', 'target="_$1" $0', $value);
        return preg_replace_callback('#data-image(?:="([^"]+?)")?#', function (array $match): string {
            $imageField = $this->fieldRoles['imageUrl'];
            return sprintf('src="%s" %s', $match[1] ?? "{{record.{$imageField}}}", $match[0]);
        }, $value);
    }
}
