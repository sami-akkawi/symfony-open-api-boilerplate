<?php declare(strict=1);

namespace App\OpenApiSpecification\ApiInfo;

use App\OpenApiSpecification\ApiException\SpecificationException;

/**
 * A URL to the Terms of Service for the API. MUST be in the format of a URL.
 * http://spec.openapis.org/oas/v3.0.3#info-object
 */

final class TermsOfService
{
    private string $termsOfService;

    private function __construct(string $termsOfService)
    {
        if (!filter_var($termsOfService, FILTER_VALIDATE_URL)) {
            throw SpecificationException::generateInvalidUrlException($termsOfService);
        }
        $this->termsOfService = $termsOfService;
    }

    public static function fromString(string $termsOfService): self
    {
        return new self($termsOfService);
    }

    public function toString(): string
    {
        return $this->termsOfService;
    }
}