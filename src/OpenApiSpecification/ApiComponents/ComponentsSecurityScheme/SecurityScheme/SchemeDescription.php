<?php declare(strict_types=1);

namespace App\OpenApiSpecification\ApiComponents\ComponentsSecurityScheme\SecurityScheme;

final class SchemeDescription
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