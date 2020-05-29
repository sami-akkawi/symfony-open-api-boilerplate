<?php declare(strict=1);

namespace App\ApiV1Bundle\ApiSpecification\ApiComponents\ComponentsParameter;

/**
 * A brief description of the parameter. This could contain examples of use.
 * http://spec.openapis.org/oas/v3.0.3#parameter-object
 */

final class ParameterDescription
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