<?php declare(strict=1);

namespace App\ApiV1Bundle\ApiSpecification\ApiComponents;

use App\ApiV1Bundle\ApiSpecification\ApiComponents\Header\HeaderDocName;
use App\ApiV1Bundle\ApiSpecification\ApiException\SpecificationException;

final class Headers
{
    /** @var Header[] */
    private array $headers;

    private function __construct(array $headers)
    {
        $this->headers = $headers;
    }

    public static function generate(): self
    {
        return new self([]);
    }

    private function hasHeader(HeaderDocName $name): bool
    {
        foreach ($this->headers as $header) {
            if ($header->getDocName()->isIdenticalTo($name)) {
                return true;
            }
        }

        return false;
    }

    public function addHeader(Header $header): self
    {
        if (!$header->hasDocName()) {
            throw SpecificationException::generateHeaderInHeadersNeedsAName();
        }
        if ($this->hasHeader($header->getDocName())) {
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
            $parameters[$header->getDocName()->toString()] = $header->toOpenApi3Specification();
        }
        return $parameters;
    }
}