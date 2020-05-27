<?php declare(strict=1);

namespace App\ApiV1Bundle\ApiSpecification\ApiException;

use App\ApiV1Bundle\ApiSpecification\ApiComponents\ComponentsReference\ReferenceType;
use App\ApiV1Bundle\ApiSpecification\ApiComponents\ComponentsSchema\Schema\DiscriminatorSchemaType;
use App\ApiV1Bundle\ApiSpecification\ApiComponents\ComponentsSecurityScheme\SecurityScheme\ApiKeyLocation;
use App\ApiV1Bundle\ApiSpecification\ApiComponents\ComponentsSecurityScheme\SecurityScheme\HttpScheme;
use App\ApiV1Bundle\ApiSpecification\ApiComponents\ComponentsSecurityScheme\SecurityScheme\SchemeType;
use LogicException;

final class SpecificationException extends LogicException
{
    private static function generate(string $message): self
    {
        $backtrace = debug_backtrace();
        if (!empty($backtrace) && !empty($backtrace[3]) && is_array($backtrace[3])) {
            $message .= PHP_EOL . $backtrace[3]['file'] . ":" . $backtrace[3]['line'];
        }
        return new self($message, 500);
    }

    public static function generateInvalidEmailException(string $invalidEmail): self
    {
        return self::generate("Invalid Email: $invalidEmail.");
    }

    public static function generateInvalidUrlException(string $invalidUrl): self
    {
        return self::generate("Invalid URL: $invalidUrl.");
    }

    public static function generateMinimumShouldBeLessThanMaximum(): self
    {
        return self::generate("The minimum value should be less than the maximum value.");
    }

    public static function generateEmptyStringException(string $field): self
    {
        return self::generate("$field cannot have an empty value.");
    }

    public static function generateEmptyEnumException(): self
    {
        return self::generate("Empty Enum.");
    }

    public static function generateEnumNotValidException(string $missingValue): self
    {
        return self::generate("$missingValue is missing in your enum.");
    }

    public static function generateDuplicateDefinitionException(string $key): self
    {
        return self::generate("$key is already defined.");
    }

    public static function generateTagNotValidException(string $tagName): self
    {
        return self::generate("$tagName was not found in the endpoint declarations.");
    }

    public static function cannotSetFormatToNonBearerHttpScheme(): self
    {
        return new self('Cannot set a bearer format to a non-bearer http scheme');
    }

    public static function generateDuplicateSecurityRequirementsException(): self
    {
        return self::generate("Duplicate security requirements.");
    }

    public static function generateInvalidReferenceType(string $invalidReferenceType): self
    {
        return self::generate("Invalid Reference Type: $invalidReferenceType. Please choose one of: " . implode(', ', ReferenceType::getValidReferenceTypes()) . '.');
    }

    public static function generateIncompatibleSchemaTypeAndFormat(string $type, string $format): self
    {
        return self::generate("The Schema Type $type is not compatible with the format $format.");
    }

    public static function generateSchemaEnumMustBeString(): self
    {
        return self::generate("The Schema Enum must be a string array.");
    }

    public static function generateObjectSchemaNeedsProperties(string $schemaName): self
    {
        return new self("$schemaName of type object needs properties.");
    }

    public static function generateInvalidApiKeyLocation(string $invalidLocation): self
    {
        return self::generate("Invalid Api Key Location: $invalidLocation. Please choose one of: " . implode(', ', ApiKeyLocation::getLocations()) . '.');
    }

    public static function generateInvalidHttpScheme(string $invalidScheme): self
    {
        return self::generate("Invalid HTTP Scheme: $invalidScheme. Please choose one of: " . implode(', ', HttpScheme::getSchemes()) . '.');
    }

    public static function generateInvalidSecuritySchemeType(string $invalidType): self
    {
        return self::generate("Invalid Security Scheme: $invalidType. Please choose one of: " . implode(', ', SchemeType::getTypes()) . '.');
    }

    public static function generateInvalidDiscriminatorSchemaType(string $invalidType): self
    {
        return self::generate("Invalid Discriminator Schema Type: $invalidType. Please choose one of: " . implode(',  ', DiscriminatorSchemaType::getTypes()) . '.');
    }

    public static function generateSchemaInSchemasNeedsAName(): self
    {
        return new self("Cannot add a schema to a list of schemas/properties if it has no name.");
    }

    public static function generateMediaTypesMustBeDefined(): self
    {
        return new self("At least one media type should be defined.");
    }

    public static function generateResponsesMustBeDefined(): self
    {
        return new self("At least one response should be defined.");
    }

    public static function generateSchemasMustBeDefined(): self
    {
        return new self("At least one schema should be defined.");
    }

    public static function generateMustHaveKeyInComponents(): self
    {
        return new self("All objects need to have keys in the components object.");
    }

    public static function generateReferenceSiblingsAreIgnored(): self
    {
        return new self('Sibling values alongside a reference element are ignored. To add properties to a reference element, wrap the element into allOf, or move the extra properties into the referenced definition (if applicable).');
    }
}