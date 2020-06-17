<?php declare(strict=1);

namespace App\ApiV1Bundle\ApiSpecification\ApiComponents\RequestBody;

/**
 * A brief description of the request body.
 * http://spec.openapis.org/oas/v3.0.3#fixed-fields-10
 */

final class RequestBodyDescription
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