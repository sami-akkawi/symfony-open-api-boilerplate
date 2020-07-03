<?php declare(strict_types=1);

namespace App\OpenApiSpecification\ApiComponents\ComponentsLink\Link\LinkParameter\ParameterKey;

use App\OpenApiSpecification\ApiException\SpecificationException;

final class KeyName
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

    public function isIdenticalTo(self $name): bool
    {
        return $this->toString() === $name->toString();
    }

    public function toOpenApiSpecification(): string
    {
        return str_replace('.', '/', $this->name);
    }
}