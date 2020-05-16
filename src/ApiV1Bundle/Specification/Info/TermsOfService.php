<?php declare(strict=1);

namespace App\ApiV1Bundle\Specification\Info;

use App\ApiV1Bundle\Specification\Exception\SpecificationException;

/**
 * A URL to the Terms of Service for the API. MUST be in the format of a URL.
 * https://swagger.io/specification/#info-object
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