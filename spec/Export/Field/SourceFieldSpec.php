<?php

declare(strict_types=1);

namespace spec\Omikron\FactFinder\Shopware6\Export\Field;

use Omikron\FactFinder\Shopware6\Export\Field\FieldInterface;
use PhpSpec\ObjectBehavior;
use Shopware\Core\Content\Category\CategoryEntity as Category;

class SourceFieldSpec extends ObjectBehavior
{
    private string $configuredFieldName = "Configured Category Path Field Name";

    public function let()
    {
        $this->beConstructedWith($this->configuredFieldName);
    }

    public function it_is_a_field()
    {
        $this->shouldHaveType(FieldInterface::class);
    }

    function it_has_a_name()
    {
        $this->getName()->shouldReturn('sourceField');
    }

    function it_it_should_export_configured_field_name(Category $category)
    {
        $this->beConstructedWith($this->configuredFieldName);

        $this->getValue($category)->shouldReturn($this->configuredFieldName);
    }
}
