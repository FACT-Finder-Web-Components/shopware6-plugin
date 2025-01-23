<?php

declare(strict_types=1);

namespace Omikron\FactFinder\Shopware6\Utilites\Ssr\Template;

use Handlebars\Loader as HandlebarsLoader;
use Omikron\FactFinder\Shopware6\Export\Filter\FilterInterface;

class Loader implements HandlebarsLoader
{
    private HandlebarsLoader $loader;
    private FilterInterface $filter;

    public function __construct(
        HandlebarsLoader $loader,
        FilterInterface $filter,
    ) {
        $this->loader = $loader;
        $this->filter = $filter;
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
