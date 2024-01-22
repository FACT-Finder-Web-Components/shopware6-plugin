<?php

declare(strict_types=1);

namespace spec\Omikron\FactFinder\Shopware6\Export\Field;

use Omikron\FactFinder\Shopware6\Export\Field\FieldInterface;
use PhpSpec\ObjectBehavior;
use Shopware\Core\Content\Category\CategoryEntity as Category;

class SourceFieldSpec extends ObjectBehavior
{
    private string $configuredFieldName = 'Configured Category Path Field Name';

    public function let(): void
    {
        $this->beConstructedWith($this->configuredFieldName);
    }

    public function it_is_a_field(): void
    {
        $this->shouldHaveType(FieldInterface::class);
    }

    public function it_has_a_name(): void
    {
        $this->getName()->shouldReturn('sourceField');
    }

    public function it_it_should_export_configured_field_name(Category $category): void
    {
        $this->beConstructedWith($this->configuredFieldName);

        $this->getValue($category)->shouldReturn($this->configuredFieldName);
    }
}
