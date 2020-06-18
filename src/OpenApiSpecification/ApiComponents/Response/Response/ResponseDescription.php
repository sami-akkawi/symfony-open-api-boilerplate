<?php declare(strict=1);

namespace App\OpenApiSpecification\ApiComponents\Response\Response;

/**
 * REQUIRED. A short description of the response. CommonMark syntax MAY be used for rich text representation.
 * http://spec.openapis.org/oas/v3.0.3#response-object
 */

final class ResponseDescription
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