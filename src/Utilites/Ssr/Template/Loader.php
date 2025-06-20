<?php

declare(strict_types=1);

namespace Omikron\FactFinder\Shopware6\Utilites\Ssr\Template;

use Handlebars\Loader as HandlebarsLoader;
use Omikron\FactFinder\Shopware6\Export\Filter\FilterInterface;

class Loader implements HandlebarsLoader
{
    public function __construct(
        private readonly HandlebarsLoader $loader,
        private readonly FilterInterface  $filter,
    ) {
    }

    /**
     * {@inheritDoc}
     */
    public function load($name)
    {
        $template = $this->loader->load($name);

        return $this->filter->filterValue((string) $template);
    }
}
