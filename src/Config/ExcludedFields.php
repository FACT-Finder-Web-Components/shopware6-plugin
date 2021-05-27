<?php


namespace Omikron\FactFinder\Shopware6\Config;


class ExcludedFields extends BaseConfig
{
    public function getDisabledPropertyGroups()
    {
        return $this->config('disabledPropertyGroups');
    }

    public function getDisabledCustomFields()
    {
        return $this->config('disabledCustomFields');
    }
}
