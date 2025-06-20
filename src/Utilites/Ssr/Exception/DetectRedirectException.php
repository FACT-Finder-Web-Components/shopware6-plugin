<?php

declare(strict_types=1);

namespace Omikron\FactFinder\Shopware6\Utilites\Ssr\Exception;

class DetectRedirectException extends \Exception
{
    public function __construct(private string $redirectUrl)
    {
        parent::__construct("Detect redirection for: $redirectUrl");
        $this->redirectUrl = $redirectUrl;
    }

    public function getRedirectUrl(): string
    {
        return $this->redirectUrl;
    }
}
