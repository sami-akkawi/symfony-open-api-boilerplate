<?php declare(strict=1);

namespace App\ApiV1Bundle\Specification\Servers;

use App\ApiV1Bundle\Specification\Exception\SpecificationException;

/**
 * REQUIRED. A URL to the target host. This URL supports Server Variables and MAY be relative, to indicate that the
 * host location is relative to the location where the OpenAPI document is being served. Variable substitutions will
 * be made when a variable is named in {brackets}.
 * https://swagger.io/specification/#server-object
 */

final class ServerUrl
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

    public function isIdenticalTo(self $url): bool
    {
        return $this->toString() === $url->toString();
    }
}