<?php declare(strict_types=1);

namespace App\OpenApiSpecification\ApiComponents\ComponentsSchema\Schema;

final class SchemaMaximumItems
{
    private int $maximumItems;

    private function __construct(int $maximumItems)
    {
        $this->maximumItems = $maximumItems;
    }

    public static function fromInt(int $maximumItems): self
    {
        return new self($maximumItems);
    }

    public function toInt(): int
    {
        return $this->maximumItems;
    }
}