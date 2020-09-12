<?php declare(strict_types=1);

namespace App\OpenApiSpecification\ApiTag\TagExternalDocs;

/**
 * A short description of the target documentation.
 * http://spec.openapis.org/oas/v3.0.3#external-documentation-object
 */

final class ExternalDocsDescription
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