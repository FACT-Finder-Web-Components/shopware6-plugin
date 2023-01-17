<?php

declare(strict_types=1);

namespace Omikron\FactFinder\Shopware6\Utilites\Ssr\Template;

use Mustache_Engine as Mustache;

class Engine
{
    private Mustache $engine;

    public function __construct(Loader $loader)
    {
        $this->engine = new Mustache(
            [
                'loader' => $loader,
                'strict_callables' => true,
            ]
        );
    }

    public function render(
        string $templateFile,
        array $context = []
    ): string {
        return $this->engine->loadTemplate($templateFile)->render($context);
    }
}
