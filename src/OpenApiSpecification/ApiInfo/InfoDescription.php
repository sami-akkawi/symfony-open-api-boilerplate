<?php declare(strict_types=1);

namespace App\OpenApiSpecification\ApiInfo;

/**
 * A short description of the API.
 * http://spec.openapis.org/oas/v3.0.3#info-object
 */

final class InfoDescription
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