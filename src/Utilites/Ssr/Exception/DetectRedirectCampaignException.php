<?php

declare(strict_types=1);

namespace Omikron\FactFinder\Shopware6\Utilites\Ssr\Exception;

class DetectRedirectCampaignException extends \Exception
{
    private string $redirectUrl;

    public function __construct(string $redirectUrl)
    {
        parent::__construct("Detect redirection for: $redirectUrl");
        $this->redirectUrl = $redirectUrl;
    }

    public function getRedirectUrl(): string
    {
        return $this->redirectUrl;
    }
}
