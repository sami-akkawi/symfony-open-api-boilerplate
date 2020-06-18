<?php declare(strict=1);

namespace App\OpenApiSpecification\ApiComponents\Schema\Schema;

final class SchemaMaximumLength
{
    private int $maximumLength;

    private function __construct(int $maximumLength)
    {
        $this->maximumLength = $maximumLength;
    }

    public static function fromInt(int $maximumLength): self
    {
        return new self($maximumLength);
    }

    public function toInt(): int
    {
        return $this->maximumLength;
    }
}