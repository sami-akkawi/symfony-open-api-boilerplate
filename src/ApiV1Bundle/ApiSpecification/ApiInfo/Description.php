<?php declare(strict=1);

namespace App\ApiV1Bundle\ApiSpecification\ApiInfo;

/**
 * A short description of the API.
 * http://spec.openapis.org/oas/v3.0.3#info-object
 */

final class Description
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