<?php declare(strict=1);

namespace App\ApiV1Bundle\ApiSpecification\ApiComponents\RequestBody;

/**
 * Determines if the request body is required in the request. Defaults to false.
 * http://spec.openapis.org/oas/v3.0.3#fixed-fields-10
 */

final class RequestBodyIsRequired
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