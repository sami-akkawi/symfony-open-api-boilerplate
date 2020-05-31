<?php declare(strict=1);

namespace App\ApiV1Bundle\ApiSpecification\ApiComponents\Parameter;

use App\ApiV1Bundle\ApiSpecification\ApiException\SpecificationException;

final class ParameterDocName
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