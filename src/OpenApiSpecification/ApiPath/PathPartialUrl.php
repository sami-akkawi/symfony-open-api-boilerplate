<?php declare(strict=1);

namespace App\OpenApiSpecification\ApiPath;

use App\OpenApiSpecification\ApiException\SpecificationException;

/**
 * A relative path to an individual endpoint. The field name MUST begin with a forward slash (/). The path is appended
 * (no relative URL resolution) to the expanded URL from the Server Object's url field in order to construct the full
 * URL. Path templating is allowed. When matching URLs, concrete (non-templated) paths would be matched before their
 * templated counterparts. Templated paths with the same hierarchy but different templated names MUST NOT exist as they
 * are identical. In case of ambiguous matching, it's up to the tooling to decide which one to use.
 * http://spec.openapis.org/oas/v3.0.3#paths-object
 */

final class PathPartialUrl
{
    private string $partialUrl;

    private function __construct(string $partialUrl)
    {
        if (empty($partialUrl)) {
            throw SpecificationException::generateEmptyStringException(self::class);
        }
        $this->partialUrl = '/' . $partialUrl;
    }

    public static function fromString(string $partialUrl): self
    {
        return new self($partialUrl);
    }

    public function toString(): string
    {
        return $this->partialUrl;
    }

    public function isIdenticalTo(self $partialUrl): bool
    {
        return $this->toString() === $partialUrl->toString();
    }
}