<?php

declare(strict_types=1);

namespace Omikron\FactFinder\Shopware6\Utilites\Ssr\Template;

use Handlebars\Handlebars;

class Engine
{
    private Handlebars $engine;

    public function __construct(Loader $loader)
    {
        $this->engine = new Handlebars(['loader' => $loader]);
    }

    public function render(string $templateFile, array $context = []): string
    {
        return $this->engine->loadTemplate($templateFile)->render($context);
    }
}
