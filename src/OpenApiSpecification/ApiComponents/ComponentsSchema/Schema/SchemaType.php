<?php declare(strict_types=1);

namespace App\OpenApiSpecification\ApiComponents\ComponentsSchema\Schema;

use App\Message\Message;
use App\OpenApiSpecification\ApiException\SpecificationException;
use DateTimeImmutable;
use LogicException;

/**
 * Primitive data types in the OAS are based on the types supported by the JSON Schema Specification Wright Draft 00.
 * Note that integer as a type is also supported and is defined as a JSON number without a fraction or exponent part.
 * null is not supported as a type (see nullable for an alternative solution). Models are defined using the Schema
 * Object, which is an extended subset of JSON Schema Specification Wright Draft 00.
 * http://spec.openapis.org/oas/v3.0.3#dataTypeFormat
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

    const STRING_BINARY_FORMAT         = 'binary';
    const STRING_BASE64_FORMAT         = 'base64';

    const STRING_BYTE_FORMAT           = 'byte';
    const STRING_DATE_FORMAT           = 'date';
    const STRING_TIME_FORMAT           = 'time';
    const STRING_DATE_TIME_ATOM_FORMAT = 'atom-date-time';
    const STRING_DATE_TIME_FORMAT      = 'date-time';
    const STRING_PASSWORD_FORMAT       = 'password';
    const STRING_REGEX_FORMAT          = 'regex';
    const STRING_UUID_FORMAT           = 'uuid';
    const STRING_EMAIL_FORMAT          = 'email';
    const STRING_URL_FORMAT            = 'url';
    const STRING_URI_FORMAT            = 'uri';

    private const VALID_STRING_FORMATS = [
        self::STRING_BINARY_FORMAT,
        self::STRING_BASE64_FORMAT,
        self::STRING_BYTE_FORMAT,
        self::STRING_DATE_FORMAT,
        self::STRING_TIME_FORMAT,
        self::STRING_DATE_TIME_ATOM_FORMAT,
        self::STRING_DATE_TIME_FORMAT,
        self::STRING_EMAIL_FORMAT,
        self::STRING_PASSWORD_FORMAT,
        self::STRING_REGEX_FORMAT,
        self::STRING_URL_FORMAT,
        self::STRING_URI_FORMAT,
        self::STRING_UUID_FORMAT,
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
        $this->enum = $enum;
        return $this;
    }

    public function setFormat(string $format): self
    {
        $this->format = $format;
        return $this;
    }

    public function isStringValueValid(string $value): ?Message
    {
        if (!strlen($value)) {
            return Message::generateError(
                'empty_string',
                'Empty String not allowed'
            );
        }

        if (!$this->format && !$this->enum) {
            return null;
        }

        if (
            $this->format === self::STRING_BINARY_FORMAT
            || $this->format === self::STRING_BASE64_FORMAT
            || $this->format === self::STRING_BYTE_FORMAT
            || $this->format === self::STRING_PASSWORD_FORMAT
            || $this->format === self::STRING_URI_FORMAT
        ) {
            return null;
        }

        if ($this->enum) {
            if (in_array($value, $this->enum)) {
                return null;
            }

            return Message::generateError(
                'value_not_allowed',
                "$value not allowed, allowed are: " . implode(', ', $this->enum),
                [
                    '%suppliedValue%' => $value,
                    '%allowedValues%' => implode(', ', $this->enum)
                ]
            );
        }

        if ($this->format === self::STRING_EMAIL_FORMAT) {
            if (filter_var($value, FILTER_VALIDATE_EMAIL, FILTER_FLAG_EMAIL_UNICODE)) {
                return null;
            }

            return $this->getInvalidStringFormatErrorMessage(self::STRING_EMAIL_FORMAT, $value);
        }

        if ($this->format === self::STRING_UUID_FORMAT) {
            if (preg_match('/^[a-f\d]{8}(-[a-f\d]{4}){3}-[a-f\d]{12}$/i', $value) === 1) {
                return null;
            }

            return $this->getInvalidStringFormatErrorMessage(self::STRING_UUID_FORMAT, $value);
        }

        if ($this->format === self::STRING_REGEX_FORMAT) {
            if (preg_match($value, '') !== false) {
                return null;
            }

            return $this->getInvalidStringFormatErrorMessage(self::STRING_REGEX_FORMAT, $value);
        }

        if ($this->format === self::STRING_DATE_FORMAT) {
            if (
                preg_match('/^[12]\d{3}-(0[1-9]|1[0-2])-(0[1-9]|[12]\d|3[01])$/', $value) === 1
                && $this->isValidStandardDate($value)
            ) {
                return null;
            }

            return $this->getInvalidStringFormatErrorMessage(self::STRING_DATE_FORMAT, $value);
        }

        if ($this->format === self::STRING_DATE_TIME_FORMAT) {
            if (
                preg_match('/^[12]\d{3}-(0[1-9]|1[0-2])-(0[1-9]|[12]\d|3[01]) ([01][0-9]|2[0-3])(:([0-5][0-9])){2}$/', $value) === 1
                && $this->isValidStandardDateTime($value)
            ) {
                return null;
            }

            return $this->getInvalidStringFormatErrorMessage(self::STRING_DATE_TIME_FORMAT, $value);
        }

        if ($this->format === self::STRING_DATE_TIME_ATOM_FORMAT) {
            if (
                preg_match('/^[12]\d{3}-(0[1-9]|1[0-2])-(0[1-9]|[12]\d|3[01])T([01][0-9]|2[0-3])(:([0-5][0-9])){2}(\+|\-)(0[0-9]|1[0-2]):([03]0)$/', $value) === 1
                && $this->isValidStandardDateTime($value)
            ) {
                return null;
            }

            return $this->getInvalidStringFormatErrorMessage(self::STRING_DATE_TIME_ATOM_FORMAT, $value);
        }

        if ($this->format === self::STRING_URL_FORMAT) {
            if (filter_var($value, FILTER_VALIDATE_URL)) {
                return null;
            }

            return $this->getInvalidStringFormatErrorMessage(self::STRING_URL_FORMAT, $value);
        }

        if ($this->format === self::STRING_TIME_FORMAT) {
            if (preg_match('/^([01][0-9]|2[0-3])(:([0-5][0-9])){2}$/', $value) === 1) {
                return null;
            }

            return $this->getInvalidStringFormatErrorMessage(self::STRING_TIME_FORMAT, $value);
        }

        throw new LogicException("Missing String validation case for format " . $this->format);
    }

    public function isAtomTime(): bool
    {
        return $this->format === self::STRING_DATE_TIME_ATOM_FORMAT;
    }

    public function isStringUrl(): bool
    {
        return $this->format === self::STRING_URL_FORMAT;
    }

    private function isValidStandardDate(string $value): bool
    {
        $dateTime = new DateTimeImmutable($value);
        return $dateTime->format('Y-m-d') === $value;
    }

    private function isValidStandardDateTime(string $value): bool
    {
        $dateTime = new DateTimeImmutable($value);
        return $dateTime->format('Y-m-d H:i:s') === $value;
    }

    private function isValidAtomDateTime(string $value): bool
    {
        $dateTime = new DateTimeImmutable($value);
        return $dateTime->format(DATE_ATOM) === $value;
    }

    public function isStringTime(): bool
    {
        return $this->format === self::STRING_TIME_FORMAT;
    }

    public function isStringUri(): bool
    {
        return $this->format === self::STRING_URI_FORMAT;
    }

    public function isBinary(): bool
    {
        return $this->format === self::STRING_BINARY_FORMAT;
    }

    private function getInvalidStringFormatErrorMessage(string $format, string $invalidValue): Message
    {
        return Message::generateError(
            'invalid_format',
            "$invalidValue is not a valid $format",
            [
                '%invalidValue%' => $invalidValue,
                '%correctFormat%' => $format
            ]
        );
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

    public static function generateBinaryString(): self
    {
        return new self(self::STRING_TYPE, self::STRING_BINARY_FORMAT);
    }

    public static function generateUrlString(): self
    {
        return new self(self::STRING_TYPE, self::STRING_URL_FORMAT);
    }

    public static function generateUriString(): self
    {
        return new self(self::STRING_TYPE, self::STRING_URI_FORMAT);
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function hasFormat(): bool
    {
        return (bool)$this->format;
    }

    public function isStringEnum(): bool
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

    public function isStringUuid(): bool
    {
        return $this->format === self::STRING_UUID_FORMAT;
    }

    public function isStringDate(): bool
    {
        return $this->format === self::STRING_DATE_FORMAT;
    }

    public function isCompatibleWithRoute(): bool
    {
        return $this->type === self::INTEGER_TYPE
            || ($this->type === self::STRING_TYPE && $this->format === null)
            || $this->format === self::STRING_UUID_FORMAT
            || $this->format === self::STRING_EMAIL_FORMAT
            || $this->format === self::STRING_DATE_FORMAT;
    }

    public function isCompatibleWith(self $type): bool
    {
        $thisType = $this->getType();
        $thatType = $type->getType();

        $thisFormat = $this->getFormat();
        $thatFormat = $type->getFormat();

        $thisEnum = $this->getEnum();
        $thatEnum = $type->getEnum();

        if ($thisType !== $thatType) {
            if (
                $thatType === self::NUMBER_TYPE
                && $thisType === self::INTEGER_TYPE
            ) {
                return true;
            }

            return false;
        }

        if (
            (is_null($thatFormat) && is_null($thatEnum))
            || $thatType === self::NUMBER_TYPE
        ) {
            return true;
        }

        if (isset($thatEnum)) {
            if (is_null($thisEnum)) {
                return false;
            }

            return !(bool)count(array_diff($thisEnum, $thatEnum));
        }

        if ($thisFormat === self::INTEGER_INT32_FORMAT && $thatFormat === self::INTEGER_INT64_FORMAT) {
            return true;
        }

        return $thisFormat === $thatFormat;
    }
}