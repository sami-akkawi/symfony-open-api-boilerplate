<?php declare(strict_types=1);

namespace App\OpenApiSpecification\ApiComponents;

use App\OpenApiSpecification\ApiComponents\ComponentsHeader\Header\HeaderKey;
use App\OpenApiSpecification\ApiException\SpecificationException;

final class ComponentsHeaders
{
    /** @var ComponentsHeader[] */
    private array $headers;

    private function __construct(array $headers)
    {
        $this->headers = $headers;
    }

    public static function generate(): self
    {
        return new self([]);
    }

    private function hasHeader(HeaderKey $key): bool
    {
        foreach ($this->headers as $header) {
            if ($header->getKey()->isIdenticalTo($key)) {
                return true;
            }
        }

        return false;
    }

    public function addHeader(ComponentsHeader $header): self
    {
        if (!$header->hasKey()) {
            throw SpecificationException::generateHeaderInHeadersNeedsAName();
        }
        if ($this->hasHeader($header->getKey())) {
            throw SpecificationException::generateDuplicateHeadersException();
        }

        return new self(array_merge($this->headers, [$header]));
    }

    public function isDefined(): bool
    {
        return (bool)count($this->headers);
    }

    public function toOpenApiSpecification(): array
    {
        if (!$this->isDefined()) {
            throw SpecificationException::generateResponsesMustBeDefined();
        }
        $parameters = [];
        foreach ($this->headers as $header) {
            $parameters[$header->getKey()->toString()] = $header->toOpenApiSpecification();
        }
        return $parameters;
    }
}