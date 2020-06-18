<?php declare(strict=1);

namespace App\OpenApiSpecification\ApiComponents\Parameter;

/**
 * Determines whether this parameter is mandatory. If the parameter location is "path", this property is REQUIRED and
 * its value MUST be true. Otherwise, the property MAY be included and its default value is false.
 * http://spec.openapis.org/oas/v3.0.3#parameter-object
 */

final class ParameterIsRequired
{
    private bool $isRequired;

    private function __construct(bool $isRequired)
    {
        $this->isRequired = $isRequired;
    }

    public static function generateTrue(): self
    {
        return new self(true);
    }

    public static function generateFalse(): self
    {
        return new self(false);
    }

    public function toBool(): bool
    {
        return $this->isRequired;
    }
}