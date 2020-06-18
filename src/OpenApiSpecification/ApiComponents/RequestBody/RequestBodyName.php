<?php declare(strict=1);

namespace App\OpenApiSpecification\ApiComponents\RequestBody;

use App\OpenApiSpecification\ApiException\SpecificationException;

final class RequestBodyName
{
    private string $name;

    private function __construct(string $name)
    {
        if (empty(trim($name))) {
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