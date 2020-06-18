<?php declare(strict_types=1);

namespace App\OpenApiSpecification\ApiComponents\SecurityScheme\SecurityScheme;

use App\OpenApiSpecification\ApiException\SpecificationException;

final class OpenIdConnectUrl
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