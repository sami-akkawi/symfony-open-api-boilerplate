<?php declare(strict_types=1);

namespace App\OpenApiSpecification\ApiComponents\ComponentsSchema\Schema;

final class SchemaMinimumItems
{
    private int $minimumItems;

    private function __construct(int $minimumItems)
    {
        $this->minimumItems = $minimumItems;
    }

    public static function fromInt(int $minimumItems): self
    {
        return new self($minimumItems);
    }

    public function toInt(): int
    {
        return $this->minimumItems;
    }
}