<?php


namespace Omikron\FactFinder\Shopware6\Config;


class ExcludedFields extends BaseConfig
{
    public function getDisabledPropertyGroups()
    {
        return parent::config('disabledPropertyGroups');
    }
}
