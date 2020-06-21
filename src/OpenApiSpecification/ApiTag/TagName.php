<?php declare(strict_types=1);

namespace App\OpenApiSpecification\ApiTag;

use App\OpenApiSpecification\ApiException\SpecificationException;

/**
 * REQUIRED. The name of the tag.
 * http://spec.openapis.org/oas/v3.0.3#tag-object
 */

final class TagName
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
}