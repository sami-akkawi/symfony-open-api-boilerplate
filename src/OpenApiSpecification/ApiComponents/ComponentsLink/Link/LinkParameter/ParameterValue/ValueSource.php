<?php declare(strict_types=1);

namespace App\OpenApiSpecification\ApiComponents\ComponentsLink\Link\LinkParameter\ParameterValue;

use App\OpenApiSpecification\ApiException\SpecificationException;

final class ValueSource
{
    private const REQUEST  = 'request';
    private const RESPONSE = 'response';

    private const VALID_SOURCES = [self::REQUEST, self::RESPONSE];

    private string $source;

    private function __construct(string $source)
    {
        if (!in_array($source, self::VALID_SOURCES)) {
            throw SpecificationException::generateInvalidParameterValueSource($source);
        }
        $this->source = $source;
    }

    public static function fromString(string $source): self
    {
        return new self($source);
    }

    public static function generateRequest(): self
    {
        return new self(self::REQUEST);
    }

    public static function generateResponse(): self
    {
        return new self(self::RESPONSE);
    }

    public function toString(): string
    {
        return $this->source;
    }

    public function toOpenApiSpecification(): string
    {
        return '$' . $this->source . '.';
    }

    public function isInResponse(): bool
    {
        return $this->source === self::RESPONSE;
    }

    public function isInRequest(): bool
    {
        return $this->source === self::REQUEST;
    }

    public static function getValidSources(): array
    {
        return self::VALID_SOURCES;
    }

    public function isIdenticalTo(self $source): bool
    {
        return $this->toString() === $source->toString();
    }
}