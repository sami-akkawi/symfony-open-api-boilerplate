<?php declare(strict=1);

namespace App\ApiV1Bundle\ApiSpecification\ApiComponents\Schema\Schema;

use App\ApiV1Bundle\ApiSpecification\ApiException\SpecificationException;

final class DiscriminatorSchemaType
{
    private const ONE_OF = 'oneOf';
    private const ANY_OF = 'anyOf';
    private const ALL_OF = 'allOf';

    private const VALID_TYPES = [self::ONE_OF, self::ANY_OF, self::ALL_OF];

    private string $type;

    private function __construct(string $type)
    {
        if (!in_array($type, self::VALID_TYPES)) {
            throw SpecificationException::generateInvalidDiscriminatorSchemaType($type);
        }
        $this->type = $type;
    }

    public function isOneOf(): bool
    {
        return $this->type === self::ONE_OF;
    }

    public function isAnyOf(): bool
    {
        return $this->type === self::ANY_OF;
    }

    public function isAllOf(): bool
    {
        return $this->type === self::ALL_OF;
    }

    public static function generateOneOf(): self
    {
        return new self(self::ONE_OF);
    }

    public static function generateAnyOf(): self
    {
        return new self(self::ANY_OF);
    }

    public static function generateAllOf(): self
    {
        return new self(self::ALL_OF);
    }

    public function toString(): string
    {
        return $this->type;
    }

    public static function getTypes(): array
    {
        return self::VALID_TYPES;
    }
}