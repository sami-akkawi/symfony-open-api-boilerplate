<?php declare(strict=1);

namespace App\OpenApiSpecification\ApiComponents\Schema\Schema;

final class SchemaDescription
{
    private string $description;

    private function __construct(string $description)
    {
        $this->description = $description;
    }

    public static function fromString(string $description): self
    {
        return new self($description);
    }

    public function toString(): string
    {
        return $this->description;
    }
}