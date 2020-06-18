<?php declare(strict=1);

namespace App\OpenApiSpecification\ApiComponents\Example;

/**
 * Embedded literal example.
 * http://spec.openapis.org/oas/v3.0.3#fixed-fields-15
 */

final class ExampleValue
{
    /** @var mixed */
    private $example;

    private function __construct($example)
    {
        $this->example = $example;
    }

    public static function generate($example): self
    {
        return new self($example);
    }

    public function toMixed()
    {
        return $this->example;
    }
}