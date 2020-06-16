<?php declare(strict=1);

namespace App\ApiV1Bundle\ApiSpecification\ApiComponents\Schema\Schema;

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