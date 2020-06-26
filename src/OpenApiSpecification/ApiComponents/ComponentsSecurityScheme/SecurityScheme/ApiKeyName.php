<?php declare(strict_types=1);

namespace App\OpenApiSpecification\ApiComponents\ComponentsSecurityScheme\SecurityScheme;

use App\OpenApiSpecification\ApiException\SpecificationException;

final class ApiKeyName
{
    private string $name;

    private function __construct(string $name)
    {
        if (empty($name)) {
            throw SpecificationException::generateEmptyStringException(self::class);
        }
        $this->name = $name;
    }

    public static function fromString(string $name): self
    {
        return new self($name);
    }

    public function toString(): string
    {
        return $this->name;
    }
}