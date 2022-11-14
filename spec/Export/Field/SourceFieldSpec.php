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

    public function it_should_export_configured_category_path_name()
    {
        $this->getName()->shouldReturn($this->configuredFieldName);
    }

    public function it_should_export_empty_string_if_category_has_no_parent(Category $category)
    {
        $this->getValue($category)->shouldReturn("");
    }

    public function it_should_return_name_written_in_lower_case()
    {
        $this->getName()->shouldReturn('sourceField');
    }
}
