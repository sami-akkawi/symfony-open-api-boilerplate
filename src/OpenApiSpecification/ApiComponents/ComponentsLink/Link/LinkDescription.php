<?php declare(strict_types=1);

namespace App\OpenApiSpecification\ApiComponents\ComponentsLink\Link;

/**
 * A description of the link.
 * http://spec.openapis.org/oas/v3.0.3#link-object
 */

final class LinkDescription
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