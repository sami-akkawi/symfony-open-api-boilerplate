<?php declare(strict_types=1);

namespace App\OpenApiSpecification;

use App\OpenApiSpecification\ApiExternalDocs\ExternalDocsDescription;
use App\OpenApiSpecification\ApiExternalDocs\ExternalDocsUrl;

/**
 * Allows referencing an external resource for extended documentation.
 * http://spec.openapis.org/oas/v3.0.3#external-documentation-object
 */

final class ApiExternalDocs
{
    private ExternalDocsUrl $url;
    private ?ExternalDocsDescription $description;

    private function __construct(ExternalDocsUrl $url, ?ExternalDocsDescription $description = null)
    {
        $this->url = $url;
        $this->description = $description;
    }

    public static function generate(string $url): self
    {
        return new self(ExternalDocsUrl::fromString($url));
    }

    public function setDescription(string $description): self
    {
        return new self($this->url, ExternalDocsDescription::fromString($description));
    }

    public function toOpenApiSpecification(): array
    {
        $specifications = [
            'url' => $this->url->toString()
        ];
        if ($this->description) {
            $specifications['description'] = $this->description->toString();
        }

        return $specifications;
    }
}