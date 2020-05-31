<?php declare(strict=1);

namespace App\ApiV1Bundle\ApiSpecification\ApiComponents\SecurityScheme\SecurityScheme;

use App\ApiV1Bundle\ApiSpecification\ApiException\SpecificationException;

final class ApiKeyLocation
{
    private const QUERY = 'query';
    private const HEADER = 'header';
    private const COOKIE = 'cookie';
    private const VALID_LOCATIONS = [self::QUERY, self::HEADER, self::COOKIE];

    private string $location;

    private function __construct(string $location)
    {
        if (!in_array($location, self::VALID_LOCATIONS)) {
            throw SpecificationException::generateInvalidApiKeyLocation($location);
        }
        $this->location = $location;
    }

    public static function fromString(string $location): self
    {
        return new self($location);
    }

    public function toString(): string
    {
        return $this->location;
    }

    public static function getLocations(): array
    {
        return self::VALID_LOCATIONS;
    }

    public static function generateInQuery(): self
    {
        return new self(self::QUERY);
    }

    public static function generateInHeader(): self
    {
        return new self(self::HEADER);
    }

    public static function generateInCookie(): self
    {
        return new self(self::COOKIE);
    }
}