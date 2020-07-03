<?php declare(strict_types=1);

namespace App\OpenApiSpecification\ApiComponents\ComponentsLink\Link\LinkParameter\ParameterKey;

use App\OpenApiSpecification\ApiException\SpecificationException;

final class KeyLocation
{
    private const QUERY  = 'query';
    private const PATH   = 'path';
    private const HEADER = 'header';
    private const COOKIE = 'cookie';
    private const BODY   = 'body';

    private const VALID_LOCATIONS = [self::QUERY, self::PATH, self::HEADER, self::COOKIE, self::BODY];

    private string $location;

    private function __construct(string $location)
    {
        if (!in_array($location, self::VALID_LOCATIONS)) {
            throw SpecificationException::generateInvalidParameterKeyLocation($location);
        }
        $this->location = $location;
    }

    public static function fromString(string $location): self
    {
        return new self($location);
    }

    public static function generateQuery(): self
    {
        return new self(self::QUERY);
    }

    public static function generatePath(): self
    {
        return new self(self::PATH);
    }

    public static function generateHeader(): self
    {
        return new self(self::HEADER);
    }

    public static function generateCookie(): self
    {
        return new self(self::COOKIE);
    }

    public static function generateBody(): self
    {
        return new self(self::BODY);
    }

    public function toString(): string
    {
        return $this->location;
    }

    public function toOpenApiSpecification(): string
    {
        return $this->location . ($this->isInBody() ? '#/' : '.');
    }

    public function isInHeader(): bool
    {
        return $this->location === self::HEADER;
    }

    public function isInPath(): bool
    {
        return $this->location === self::PATH;
    }

    public function isInQuery(): bool
    {
        return $this->location === self::QUERY;
    }

    public function isInCookie(): bool
    {
        return $this->location === self::COOKIE;
    }

    public function isInBody(): bool
    {
        return $this->location === self::BODY;
    }

    public static function getValidLocations(): array
    {
        return self::VALID_LOCATIONS;
    }

    public function isIdenticalTo(self $location): bool
    {
        return $this->toString() === $location->toString();
    }
}