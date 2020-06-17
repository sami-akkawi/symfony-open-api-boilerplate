<?php declare(strict=1);

namespace App\ApiV1Bundle\ApiSpecification\ApiComponents\Header;
/**
 * A brief description of the Header. This could contain examples of use.
 * http://spec.openapis.org/oas/v3.0.3#Header-object
 */

final class HeaderDescription
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