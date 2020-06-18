<?php declare(strict=1);

namespace App\OpenApiSpecification\ApiPath\PathOperation;

/**
 * A verbose explanation of the operation behavior.
 * http://spec.openapis.org/oas/v3.0.3#operation-object
 */

final class OperationDescription
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