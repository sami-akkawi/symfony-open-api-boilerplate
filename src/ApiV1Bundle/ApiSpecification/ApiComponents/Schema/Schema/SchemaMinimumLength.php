<?php declare(strict=1);

namespace App\ApiV1Bundle\ApiSpecification\ApiComponents\Schema\Schema;

final class SchemaMinimumLength
{
    private int $minimumLength;

    private function __construct(int $minimumLength)
    {
        $this->minimumLength = $minimumLength;
    }

    public static function fromInt(int $minimumLength): self
    {
        return new self($minimumLength);
    }

    public function toInt(): int
    {
        return $this->minimumLength;
    }
}