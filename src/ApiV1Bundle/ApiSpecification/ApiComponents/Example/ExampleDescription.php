<?php declare(strict=1);

namespace App\ApiV1Bundle\ApiSpecification\ApiComponents\Example;

/**
 * Long description for the example.
 * http://spec.openapis.org/oas/v3.0.3#fixed-fields-15
 */

final class ExampleDescription
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