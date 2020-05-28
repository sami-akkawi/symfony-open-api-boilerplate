<?php declare(strict=1);

namespace App\ApiV1Bundle\ApiSpecification\ApiInfo\Contact;

use App\ApiV1Bundle\ApiSpecification\ApiException\SpecificationException;

/**
 * The URL pointing to the contact information. MUST be in the format of a URL.
 * http://spec.openapis.org/oas/v3.0.3#contact-object
 */

final class ContactUrl
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