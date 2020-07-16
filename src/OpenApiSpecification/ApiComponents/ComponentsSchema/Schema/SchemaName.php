<?php declare(strict_types=1);

namespace App\OpenApiSpecification\ApiComponents\ComponentsSchema\Schema;

use App\OpenApiSpecification\ApiException\SpecificationException;

final class SchemaName
{
    private string $name;

    private function __construct(string $name)
    {
        if (!strlen($name)) {
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

    public function isIdenticalTo(self $name): bool
    {
        return $this->toString() === $name->toString();
    }
}