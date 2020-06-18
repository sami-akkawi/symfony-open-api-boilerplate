<?php declare(strict_types=1);

namespace App\OpenApiSpecification\ApiServers\ServerVariable;

/**
 * An optional description for the server variable.
 * http://spec.openapis.org/oas/v3.0.3#server-variable-object
 */

final class VariableDescription
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