<?php declare(strict_types=1);

namespace App\OpenApiSpecification\ApiException;

use App\OpenApiSpecification\ApiComponents\ComponentsLink\Link\LinkParameter\ParameterKey\KeyLocation;
use App\OpenApiSpecification\ApiComponents\ComponentsLink\Link\LinkParameter\ParameterValue\ValueLocation;
use App\OpenApiSpecification\ApiComponents\ComponentsLink\Link\LinkParameter\ParameterValue\ValueSource;
use App\OpenApiSpecification\ApiComponents\ComponentsParameter\Parameter\ParameterLocation;
use App\OpenApiSpecification\ApiComponents\ComponentsParameter\Parameter\ParameterStyle;
use App\OpenApiSpecification\ApiComponents\Reference\ReferenceType;
use App\OpenApiSpecification\ApiComponents\ComponentsSchema\Schema\DiscriminatorSchemaType;
use App\OpenApiSpecification\ApiComponents\ComponentsSecurityScheme\SecurityScheme\ApiKeyLocation;
use App\OpenApiSpecification\ApiComponents\ComponentsSecurityScheme\SecurityScheme\HttpScheme;
use App\OpenApiSpecification\ApiComponents\ComponentsSecurityScheme\SecurityScheme\SchemeType;
use App\OpenApiSpecification\ApiPath\PathOperation\OperationName;
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

    public static function generateMinimumItemsCannotBeLessThanZero(): self
    {
        return self::generate("The minimum items allowed cannot be less than zero.");
    }

    public static function generateMinimumLengthCannotBeLessThanZero(): self
    {
        return self::generate("The minimum length allowed cannot be less than zero.");
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

    public static function generateDuplicateParameters(): self
    {
        return self::generate("Duplicate parameter definitions.");
    }

    public static function generateDuplicateExamples(): self
    {
        return self::generate("Duplicate example definitions.");
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

    public static function generateRequestBodyInRequestBodiesNeedsAName(): self
    {
        return new self("Cannot add a request body to a list of request bodies if it has no name.");
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

    public static function generateInvalidNameInHeader(string $invalidName): self
    {
        return self::generate("$invalidName may not be defined in header.");
    }

    public static function generateInvalidParameterLocation(string $invalidLocation): self
    {
        return self::generate("Invalid Parameter Location: $invalidLocation. Please choose one of: " . implode(', ', ParameterLocation::getValidLocations()) . '.');
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

    public static function generateCannotBeInPath(string $parameterType): self
    {
        return new self("Parameter of type '$parameterType' cannot be defined in path.");
    }

    public static function generateParameterMustBeDefinedToBeRequired(string $parameterName): self
    {
        return new self("Parameter '$parameterName' must be defined to be set as required.");
    }

    public static function generateInvalidStyle(string $invalidStyle): self
    {
        return self::generate("Invalid Parameter Style: $invalidStyle. Please choose one of: " . implode(', ', ParameterStyle::getValidStyles()) . '.');
    }

    public static function generateStyleNotSupportedForType(string $invalidStyle, string $type): self
    {
        return self::generate("Invalid Parameter Styling. Parameter of type '$type' cannot by styled as '$invalidStyle'");
    }

    public static function generateStyleNotSupportedForLocation(string $invalidStyle, string $location): self
    {
        return self::generate("Invalid Parameter Styling. Parameter in '$location' cannot by styled as '$invalidStyle'");
    }

    public static function generateHeaderInHeadersNeedsAName(): self
    {
        return new self("Cannot add a header to a list of headers if it has no name.");
    }

    public static function generateDuplicateHeadersException(): self
    {
        return self::generate("Duplicate headers.");
    }

    public static function generateRequireOnlyWorksOnlyOnAllOf(): self
    {
        return self::generate("Cannot requireOnly properties on a 'oneOf' or an 'anyOf'!");
    }

    public static function generateInvalidOperationName(string $invalidOperation): self
    {
        return self::generate("Invalid Operation: $invalidOperation. Please choose one of: " . implode(', ', OperationName::getValidOperations()) . '.');
    }

    public static function generateInvalidOperationTags(): self
    {
        return self::generate("Operation tags must be strings.");
    }

    public static function generatePathOperationAlreadyDefined(string $operation): self
    {
        return new self("The operation '$operation' was already defined on path.");
    }

    public static function generateInvalidOperationPartialUrl(string $invalidUrl, string $urlEncodedUrl): self
    {
        return self::generate("The Operation Partial URL '$invalidUrl' is invalid as it would be encoded to '$urlEncodedUrl', try to use latin letters only.");
    }

    public static function generatePathParameterNotDefinedInUrl(string $pathParameter, string $operationId): self
    {
        return self::generate("The path parameter '$pathParameter' was not defined in the url of the operation '$operationId'.");
    }

    public static function generatePathParameterNotDefinedAsSuch(string $pathParameter, string $operationId): self
    {
        return self::generate("The parameter '$pathParameter' found in the path was not defined as such oin the operation '$operationId'.");
    }

    public static function generateCannotAddPrimitiveSchemaToAllOfDiscriminator(): self
    {
        return self::generate("Cannot add a primitive Schema to an 'allOf' Discriminator Schema, make sure to wrap all the primitives in one or more Schema(s).");
    }

    public static function generateInvalidParameterKeyLocation(string $invalidLocation): self
    {
        return self::generate("Invalid Parameter Key Location: $invalidLocation. Please choose one of: " . implode(', ', KeyLocation::getValidLocations()) . '.');
    }

    public static function generateInvalidParameterValueLocation(string $invalidLocation): self
    {
        return self::generate("Invalid Parameter Value Location: $invalidLocation. Please choose one of: " . implode(', ', ValueLocation::getValidLocations()) . '.');
    }

    public static function generateInvalidParameterValueSource(string $invalidSource): self
    {
        return self::generate("Invalid Parameter Value Source: $invalidSource. Please choose one of: " . implode(', ', ValueSource::getValidSources()) . '.');
    }

    public static function generateResponseParameterValueCanOnlyHaveBodyLocation(): self
    {
        return self::generate("Parameter Value from Response cannot be outside of body.");
    }

    public static function generatePathOperationIdAlreadyDefined(string $operationId): self
    {
        return new self("The operation id '$operationId' was already defined on path.");
    }

    public static function generateTargetOperationDoesNotExist(string $linkOperationId, string $sourceOperationId): self
    {
        return new self("The operation '$linkOperationId' defined in a response of '$sourceOperationId' is not defined.");
    }

    public static function generateLinkHasNoParameters(string $linkOperationId, string $sourceOperationId): self
    {
        return new self("The link to '$linkOperationId' defined in a response of '$sourceOperationId' has no parameters.");
    }

    public static function generateTargetParameterNotDefined(string $key, string $linkOperationId, string $sourceOperationId): self
    {
        return new self("The parameter $key was not found in '$linkOperationId' as defined in '$sourceOperationId'.");
    }

    public static function generateSourceParameterNotDefined(string $key, string $sourceOperationId): self
    {
        return new self("The parameter $key was not found in '$sourceOperationId' as defined in a belonging link.");
    }

    public static function generateParametersNotCompatible(string $keyParameter, string $linkOperationId, string $valueParameter, string $sourceOperationId): self
    {
        return new self("The parameter '$valueParameter' in '$sourceOperationId' is not compatible with '$keyParameter' of '$linkOperationId'.");
    }
}