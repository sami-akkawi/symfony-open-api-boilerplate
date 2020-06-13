<?php declare(strict=1);

namespace App\ApiV1Bundle\ApiSpecification\ApiComponents\Parameter;

use App\ApiV1Bundle\ApiSpecification\ApiException\SpecificationException;

/**
 * REQUIRED. The location of the parameter. Possible values are "query", "header", "path" or "cookie".
 * http://spec.openapis.org/oas/v3.0.3#parameter-object
 */

final class ParameterLocation
{
    private const QUERY  = 'query';
    private const PATH   = 'path';
    private const HEADER = 'header';
    private const COOKIE = 'cookie';

    private const VALID_LOCATIONS = [self::QUERY, self::PATH, self::HEADER, self::COOKIE];

    private string $location;

    private function __construct(string $location)
    {
        if (!in_array($location, self::VALID_LOCATIONS)) {
            throw SpecificationException::generateInvalidParameterLocation($location);
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

    public function toString(): string
    {
        return $this->location;
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

    public static function getValidLocations(): array
    {
        return self::VALID_LOCATIONS;
    }

    public function isIdenticalTo(self $location): bool
    {
        return $this->toString() === $location->toString();
    }
}