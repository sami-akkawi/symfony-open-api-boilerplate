<?php declare(strict_types=1);

namespace App\OpenApiSpecification\ApiComponents\Schema\Schema;

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