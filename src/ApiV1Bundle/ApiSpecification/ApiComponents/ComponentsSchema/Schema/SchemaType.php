<?php declare(strict=1);
// Created by sami-akkawi on 16.05.20

namespace App\ApiV1Bundle\ApiSpecification\ApiComponents\ComponentsSchema\Schema;

use App\ApiV1Bundle\ApiSpecification\ApiException\SpecificationException;

/**
 * Primitive data types in the OAS are based on the types supported by the JSON Schema Specification Wright Draft 00.
 * Note that integer as a type is also supported and is defined as a JSON number without a fraction or exponent part.
 * null is not supported as a type (see nullable for an alternative solution). Models are defined using the Schema
 * Object, which is an extended subset of JSON Schema Specification Wright Draft 00.
 * https://swagger.io/specification/#dataTypeFormat
 */

final class SchemaType
{
    private const ARRAY_TYPE      = 'array';
    private const INTEGER_TYPE    = 'integer';
    private const NUMBER_TYPE     = 'number';
    private const STRING_TYPE     = 'string';
    private const BOOLEAN_TYPE    = 'boolean';
    private const OBJECT_TYPE     = 'object';

    const INTEGER_INT32_FORMAT    = 'int32';
    const INTEGER_INT64_FORMAT    = 'int64';

    const NUMBER_FLOAT_FORMAT     = 'float';
    const NUMBER_DOUBLE_FORMAT    = 'double';

    const STRING_BYTE_FORMAT      = 'byte';
    const STRING_BINARY_FORMAT    = 'binary';
    const STRING_DATE_FORMAT      = 'date';
    const STRING_DATE_TIME_FORMAT = 'date-time';
    const STRING_PASSWORD_FORMAT  = 'password';
    const STRING_UUID_FORMAT      = 'uuid';
    const STRING_EMAIL_FORMAT     = 'email';
    const STRING_URL_FORMAT       = 'url';

    private const VALID_STRING_FORMATS = [
        self::STRING_BYTE_FORMAT,
        self::STRING_BINARY_FORMAT,
        self::STRING_DATE_FORMAT,
        self::STRING_DATE_TIME_FORMAT,
        self::STRING_PASSWORD_FORMAT,
        self::STRING_UUID_FORMAT,
        self::STRING_EMAIL_FORMAT,
        self::STRING_URL_FORMAT
    ];
    private const VALID_INTEGER_FORMATS = [
        self::INTEGER_INT32_FORMAT,
        self::INTEGER_INT64_FORMAT
    ];
    private const VALID_NUMBER_FORMATS = [
        self::NUMBER_FLOAT_FORMAT,
        self::NUMBER_DOUBLE_FORMAT
    ];

    private string $type;
    private ?string $format;
    private ?array $enum;

    private function __construct(string $type, ?string $format = null, ?array $enum = null)
    {
        $this->type = $type;
        if (!$this->formatIsCompatibleWithType($format)) {
            throw SpecificationException::generateIncompatibleSchemaTypeAndFormat($type, $format);
        }
        $this->format = $format;
        if (!$this->isEnumValid($enum)) {
            throw SpecificationException::generateSchemaEnumMustBeString();
        }
        $this->enum = $enum;
    }

    public function setEnum(array $enum): self
    {
        return new self($this->type, $this->format, $enum);
    }

    public function setFormat(string $format): self
    {
        return new self($this->type, $format, $this->enum);
    }

    private function isEnumValid(?array $enum): bool
    {
        if ($enum === null) {
            return true;
        }

        foreach ($enum as $item) {
            if (!is_string($item)) {
                return false;
            }
        }

        if (count($enum) === 0) {
            return false;
        }

        return true;
    }

    private function formatIsCompatibleWithType(?string $format): bool
    {
        if ($format === null) {
            return true;
        }
        if (self::STRING_TYPE === $this->type) {
            return in_array($format, self::VALID_STRING_FORMATS);
        }
        if (self::INTEGER_TYPE === $this->type) {
            return in_array($format, self::VALID_INTEGER_FORMATS);
        }
        if (self::NUMBER_TYPE === $this->type) {
            return in_array($format, self::VALID_NUMBER_FORMATS);
        }

        return false;
    }

    public static function generateString(): self
    {
        return new self(self::STRING_TYPE);
    }

    public static function generateInteger(): self
    {
        return new self(self::INTEGER_TYPE);
    }

    public static function generateNumber(): self
    {
        return new self(self::NUMBER_TYPE);
    }

    public static function generateBoolean(): self
    {
        return new self(self::BOOLEAN_TYPE);
    }

    public static function generateObject(): self
    {
        return new self(self::OBJECT_TYPE);
    }

    public static function generateArray(): self
    {
        return new self(self::ARRAY_TYPE);
    }

    public static function generateUuidString(): self
    {
        return new self(self::STRING_TYPE, self::STRING_UUID_FORMAT);
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function hasFormat(): bool
    {
        return (bool)$this->format;
    }

    public function isEnum(): bool
    {
        return self::STRING_TYPE === $this->type && !empty($this->enum);
    }

    public function getFormat(): ?string
    {
        return $this->format;
    }

    public function getEnum(): ?array
    {
        return $this->enum;
    }

    public function isArray(): bool
    {
        return self::ARRAY_TYPE === $this->type;
    }

    public function isObject(): bool
    {
        return self::OBJECT_TYPE === $this->type;
    }
}