<?php declare(strict_types=1);

namespace App\OpenApiSpecification\ApiComponents\ComponentsSecurityScheme\SecurityScheme;

use App\OpenApiSpecification\ApiException\SpecificationException;

final class SchemeType
{
    private const API_KEY = 'apiKey';
    private const HTTP = 'http';
    private const O_AUTH_2 = 'oauth2';
    private const OPEN_ID_CONNECT = 'openIdConnect';
    private const VALID_TYPES = [self::API_KEY, self::HTTP, self::O_AUTH_2, self::OPEN_ID_CONNECT];

    private string $type;

    private function __construct(string $type)
    {
        if (!in_array($type, self::VALID_TYPES)) {
            throw SpecificationException::generateInvalidSecuritySchemeType($type);
        }
        $this->type = $type;
    }

    public static function fromString(string $type): self
    {
        return new self($type);
    }

    public function toString(): string
    {
        return $this->type;
    }

    public static function getTypes(): array
    {
        return self::VALID_TYPES;
    }

    public static function generateApiKey(): self
    {
        return new self(self::API_KEY);
    }

    public static function generateHttp(): self
    {
        return new self(self::HTTP);
    }

    public static function generateOpenIdConnect(): self
    {
        return new self(self::OPEN_ID_CONNECT);
    }
}