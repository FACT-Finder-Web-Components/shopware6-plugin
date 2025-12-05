<?php

declare(strict_types=1);

namespace Omikron\FactFinder\Shopware6\Twig;

use Omikron\FactFinder\Shopware6\Config\Communication;
use Symfony\Component\HttpFoundation\RequestStack;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class FactFinderExtension extends AbstractExtension
{
    public function __construct(private Communication $config, private RequestStack $requestStack)
    {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('ff_disable_webc', [$this, 'disableWebc']),
        ];
    }

    public function disableWebc(): bool
    {
        $request = $this->requestStack->getCurrentRequest();

        if (!$request) {
            return false;
        }

        $context = $request->attributes->get('sw-sales-channel-context');

        if (!$context) {
            return false;
        }

        $salesChannelId = $context->getSalesChannel()->getId();

        return $this->config->disableFFWebc($salesChannelId);
    }
}
