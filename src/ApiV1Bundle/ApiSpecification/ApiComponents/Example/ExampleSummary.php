<?php declare(strict=1);

namespace App\ApiV1Bundle\ApiSpecification\ApiComponents\Example;

/**
 * Short description for the example.
 * http://spec.openapis.org/oas/v3.0.3#fixed-fields-15
 */

final class ExampleSummary
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