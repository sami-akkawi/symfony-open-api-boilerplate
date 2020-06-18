<?php declare(strict_types=1);

namespace App\OpenApiSpecification\ApiTag;

/**
 * A short description for the tag.
 * http://spec.openapis.org/oas/v3.0.3#tag-object
 */

final class TagDescription
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