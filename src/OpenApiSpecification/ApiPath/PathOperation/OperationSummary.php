<?php declare(strict=1);

namespace App\OpenApiSpecification\ApiPath\PathOperation;

/**
 * A short summary of what the operation does.
 * http://spec.openapis.org/oas/v3.0.3#operation-object
 */

final class OperationSummary
{
    private string $summary;

    private function __construct(string $summary)
    {
        $this->summary = $summary;
    }

    public static function fromString(string $summary): self
    {
        return new self($summary);
    }

    public function toString(): string
    {
        return $this->summary;
    }
}