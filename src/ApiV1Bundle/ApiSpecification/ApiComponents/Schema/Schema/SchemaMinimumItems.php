<?php declare(strict=1);

namespace App\ApiV1Bundle\ApiSpecification\ApiComponents\Schema\Schema;

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