<?php declare(strict_types=1);

namespace App\OpenApiSpecification\ApiComponents;

use App\OpenApiSpecification\ApiComponents\Reference\ReferenceObjectName;
use App\OpenApiSpecification\ApiComponents\Reference\ReferenceType;

/**
 * A simple object to allow referencing other components in the specification, internally and externally.
 * http://spec.openapis.org/oas/v3.0.3#reference-object
 */

final class Reference
{
    private const REFERENCE_PREFIX = '#/components/';
    private ReferenceType $type;
    private ReferenceObjectName $objectName;

    public function __construct(ReferenceType $type, ReferenceObjectName $objectName)
    {
        $this->type = $type;
        $this->objectName = $objectName;
    }

    public static function generateSchemaReference(string $objectName): self
    {
        return new self(
            ReferenceType::generateSchemaType(),
            ReferenceObjectName::fromString($objectName)
        );
    }

    public static function generateResponseReference(string $objectName): self
    {
        return new self(
            ReferenceType::generateResponseType(),
            ReferenceObjectName::fromString($objectName)
        );
    }

    public static function generateParameterReference(string $objectName): self
    {
        return new self(
            ReferenceType::generateParameterType(),
            ReferenceObjectName::fromString($objectName)
        );
    }

    public static function generateRequestBodyReference(string $objectName): self
    {
        return new self(
            ReferenceType::generateRequestBodyType(),
            ReferenceObjectName::fromString($objectName)
        );
    }

    public static function generateExampleReference(string $objectName): self
    {
        return new self(
            ReferenceType::generateExampleType(),
            ReferenceObjectName::fromString($objectName)
        );
    }

    public static function generateLinkReference(string $objectName): self
    {
        return new self(
            ReferenceType::generateLinkType(),
            ReferenceObjectName::fromString($objectName)
        );
    }

    public static function generateHeaderReference(string $objectName): self
    {
        return new self(
            ReferenceType::generateHeaderType(),
            ReferenceObjectName::fromString($objectName)
        );
    }

    public static function generateSecuritySchemeReference(string $objectName): self
    {
        return new self(
            ReferenceType::generateSecuritySchemeType(),
            ReferenceObjectName::fromString($objectName)
        );
    }

    public function getStringName(): string
    {
        return $this->objectName->toString();
    }

    public function toOpenApiSpecification(): array
    {
        return ['$ref' => self::REFERENCE_PREFIX . $this->type->toReferenceLinkPart() . $this->objectName->toString()];
    }
}