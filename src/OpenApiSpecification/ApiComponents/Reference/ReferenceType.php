<?php declare(strict_types=1);

namespace App\OpenApiSpecification\ApiComponents\Reference;

use App\OpenApiSpecification\ApiException\SpecificationException;

/**
 * REQUIRED. The reference string.
 * http://spec.openapis.org/oas/v3.0.3#reference-object
 */

final class ReferenceType
{
    private const SCHEMA_TYPE = 'schema';
    private const RESPONSE_TYPE = 'response';
    private const PARAMETER_TYPE = 'parameter';
    private const EXAMPLE_TYPE = 'example';
    private const REQUEST_BODY_TYPE = 'requestBody';
    private const HEADER_TYPE = 'header';
    private const SECURITY_SCHEME_TYPE = 'securityScheme';
    private const LINK_TYPE = 'link';

    private const VALID_TYPES = [
        self::SCHEMA_TYPE,
        self::RESPONSE_TYPE,
        self::PARAMETER_TYPE,
        self::EXAMPLE_TYPE,
        self::REQUEST_BODY_TYPE,
        self::HEADER_TYPE,
        self::SECURITY_SCHEME_TYPE,
        self::LINK_TYPE
    ];

    private string $type;

    private function __construct(string $type)
    {
        if (!in_array($type, self::VALID_TYPES)) {
            throw SpecificationException::generateInvalidReferenceType($type);
        }
        $this->type = $type;
    }

    public static function generateSchemaType(): self
    {
        return new self(self::SCHEMA_TYPE);
    }

    public static function generateResponseType(): self
    {
        return new self(self::RESPONSE_TYPE);
    }

    public static function generateParameterType(): self
    {
        return new self(self::PARAMETER_TYPE);
    }

    public static function generateExampleType(): self
    {
        return new self(self::EXAMPLE_TYPE);
    }

    public static function generateRequestBodyType(): self
    {
        return new self(self::REQUEST_BODY_TYPE);
    }

    public static function generateHeaderType(): self
    {
        return new self(self::HEADER_TYPE);
    }

    public static function generateSecuritySchemeType(): self
    {
        return new self(self::SECURITY_SCHEME_TYPE);
    }

    public static function generateLinkType(): self
    {
        return new self(self::LINK_TYPE);
    }

    public static function getValidReferenceTypes(): array
    {
        return self::VALID_TYPES;
    }

    public function toString(): string
    {
        return $this->type;
    }

    public function toReferenceLinkPart(): string
    {
        if ($this->toString() === self::REQUEST_BODY_TYPE) {
            return 'requestBodies/';
        }

        return $this->type . 's/';
    }

    public function isIdenticalTo(self $objectName): bool
    {
        return $this->toString() === $objectName->toString();
    }
}