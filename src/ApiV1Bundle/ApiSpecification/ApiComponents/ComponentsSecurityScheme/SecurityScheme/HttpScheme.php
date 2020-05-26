<?php declare(strict=1);

namespace App\ApiV1Bundle\ApiSpecification\ApiComponents\ComponentsSecurityScheme\SecurityScheme;

use App\ApiV1Bundle\ApiSpecification\ApiException\SpecificationException;

final class HttpScheme
{
    private const BASIC = 'basic';
    private const BEARER = 'bearer';
    private const DIGEST = 'digest';
    private const O_AUTH = 'oauth';

    private const VALID_SCHEMES = [self::BASIC, self::BEARER, self::DIGEST, self::O_AUTH];

    private string $scheme;

    private function __construct(string $scheme)
    {
        if (!in_array($scheme, self::VALID_SCHEMES)) {
            throw SpecificationException::generateInvalidHttpScheme($scheme);
        }
        $this->scheme = $scheme;
    }

    public static function fromString(string $scheme): self
    {
        return new self($scheme);
    }

    public function toString(): string
    {
        return $this->scheme;
    }

    public static function getSchemes(): array
    {
        return self::VALID_SCHEMES;
    }

    public static function generateBasic(): self
    {
        return new self(self::BASIC);
    }

    public static function generateBearer(): self
    {
        return new self(self::BEARER);
    }

    public static function generateDigest(): self
    {
        return new self(self::DIGEST);
    }

    public static function generateOAuth(): self
    {
        return new self(self::O_AUTH);
    }

    public function isBearer(): bool
    {
        return $this->scheme === self::BEARER;
    }
}