<?php declare(strict_types=1);

namespace App\OpenApiSpecification\ApiExternalDocs;

use App\OpenApiSpecification\ApiException\SpecificationException;

/**
 * REQUIRED. The URL for the target documentation. Value MUST be in the format of a URL.
 * http://spec.openapis.org/oas/v3.0.3#external-documentation-object
 */

final class ExternalDocsUrl
{
    private string $url;

    private function __construct(string $url)
    {
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            throw SpecificationException::generateInvalidUrlException($url);
        }
        $this->url = $url;
    }

    public static function fromString(string $url): self
    {
        return new self($url);
    }

    public function toString(): string
    {
        return $this->url;
    }
}