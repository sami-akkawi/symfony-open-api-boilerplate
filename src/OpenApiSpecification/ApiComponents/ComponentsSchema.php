<?php declare(strict_types=1);

namespace App\OpenApiSpecification\ApiComponents;

use App\OpenApiSpecification\ApiComponents\ComponentsSchema\ArraySchema;
use App\OpenApiSpecification\ApiComponents\ComponentsSchema\BooleanSchema;
use App\OpenApiSpecification\ApiComponents\ComponentsSchema\DiscriminatorSchema;
use App\OpenApiSpecification\ApiComponents\ComponentsSchema\IntegerSchema;
use App\OpenApiSpecification\ApiComponents\ComponentsSchema\MapSchema;
use App\OpenApiSpecification\ApiComponents\ComponentsSchema\NumberSchema;
use App\OpenApiSpecification\ApiComponents\ComponentsSchema\ObjectSchema;
use App\OpenApiSpecification\ApiComponents\ComponentsSchema\PrimitiveSchema;
use App\OpenApiSpecification\ApiComponents\ComponentsSchema\ReferenceSchema;
use App\OpenApiSpecification\ApiComponents\ComponentsSchema\Schema;
use App\OpenApiSpecification\ApiComponents\ComponentsSchema\Schema\SchemaIsDeprecated;
use App\OpenApiSpecification\ApiComponents\ComponentsSchema\Schema\SchemaIsNullable;
use App\OpenApiSpecification\ApiComponents\ComponentsSchema\Schema\SchemaIsRequired;
use App\OpenApiSpecification\ApiComponents\ComponentsSchema\Schema\SchemaName;
use App\OpenApiSpecification\ApiComponents\ComponentsSchema\Schema\SchemaType;
use App\OpenApiSpecification\ApiComponents\ComponentsSchema\StringSchema;
use App\OpenApiSpecification\ApiException\SpecificationException;
use LogicException;

/**
 * The Schema Object allows the definition of input and output data types. These types can be objects, but also
 * primitives and arrays. This object is an extended subset of the JSON Schema Specification Wright Draft 00.
 * http://spec.openapis.org/oas/v3.0.3#schema-object
 */

abstract class ComponentsSchema
{
    protected SchemaIsNullable $isNullable;
    protected ?SchemaName $name;
    protected SchemaIsRequired $isRequired;
    protected ?ComponentsExample $example;
    protected SchemaIsDeprecated $isDeprecated;

    abstract public function toOpenApiSpecification(): array;

    abstract public function setName(string $name);

    public function hasName(): bool
    {
        return (bool)$this->name;
    }

    public function getName(): ?SchemaName
    {
        return $this->name;
    }

    public function isNullable(): bool
    {
        return $this->isNullable->toBool();
    }

    public function isRequired(): bool
    {
        return $this->isRequired->toBool();
    }

    abstract public function unRequire();

    abstract public function require();

    abstract public function deprecate();

    abstract public function makeNullable();

    abstract public function setExample(ComponentsExample $example);

    abstract public function isValueValid($value): array;

    public function validateValue($value): ?SpecificationException
    {
        $errors = $this->isValueValid($value);
        if ($errors) {
            $defaultTexts = [];
            foreach ($errors as $error) {
                $defaultTexts[] = $error->getDefaultText()->toString();
            }
            return new SpecificationException(implode(PHP_EOL, $defaultTexts));
        }
        return null;
    }

    abstract public function toSchema(): Schema;

    abstract public function getType(): ?SchemaType;

    public function getValueFromCastedString(string $value)
    {
        $cleanValue = trim($value);
        if (
            $this->isNullable->toBool()
            && (strlen($cleanValue) === 0 || strcasecmp($value, 'null') === 0)
        ) {
            return null;
        }

        return $this->getValueFromTrimmedCastedString($cleanValue);
    }

    abstract protected function getValueFromTrimmedCastedString(string $value);


    public function findSchemaByName(string $name): ?Schema
    {
        $parts = explode('.', $name);
        $schema = $this;
        foreach ($parts as $index => $part) {
            if (is_null($schema)) {
                return null;
            }

            if ($schema instanceof ReferenceSchema) {
                $schema = $schema->toSchema();
            }

            if (
                isset($parts[$index + 1])
                && (
                    $schema instanceof PrimitiveSchema
                    || $schema instanceof ArraySchema
                    || $schema instanceof MapSchema
                )
            ) {
                return null;
            }

            if ($schema instanceof ObjectSchema) {
                $schema = $schema->getProperties()->findSchemaByName($part);
                continue;
            }

            if ($schema instanceof DiscriminatorSchema) {
                $schema = $schema->getFirstSchemaByName($part);
                continue;
            }

            throw new LogicException("Missing case of link validation for " . get_class($schema));
        }

        return $schema;
    }

    public function isCompatibleWith(self $schema): bool
    {
        if ($schema instanceof PrimitiveSchema) {
            if (!$this->getType()->isCompatibleWith($schema->getType())) {
                return false;
            }

            if ($schema instanceof BooleanSchema) {
                return true;
            }

            if ($schema instanceof StringSchema) {
                return $this->isStringSchemaCompatible($schema);
            }

            if (
                $schema instanceof NumberSchema
                || $schema instanceof IntegerSchema
            ) {
                return $this->isNumberSchemaCompatible($schema);
            }

            throw new LogicException("Missing primitive compatibility check for " .  get_class($this));
        }

        if ($schema instanceof ArraySchema) {
            if ($this instanceof ArraySchema) {
                return $this->getItemType()->isCompatibleWith($schema->getItemType());
            }

            return false;
        }

        // todo: if ObjectSchema then check that require properties are defined
        //  and that all field are compatible
        //  and that there are no extra fields

        // todo: if Discriminator, then according to type of discriminator
        //  if AllOf, then handle as Object
        //  if AnyOf, then $this should be compatible to any one definition
        //  if OneOf, then TODO: define this case

        // todo: if Map, define this case

        throw new LogicException("Missing compatibility check for " . get_class($this));
    }

    /** @param NumberSchema|IntegerSchema */
    private function isNumberSchemaCompatible($schema): bool
    {
        $minimumIsCompatible = false;
        $thatMinimum = $schema->getMinimum();
        $thisMinimum = $this->getMinimum();
        if (
            !$thatMinimum
            || (
                $thisMinimum
                && $thisMinimum->toFloat() > $thatMinimum->toFloat()
            )
        ) {
            $minimumIsCompatible = true;
        }

        $maximumIsCompatible = false;
        $thatMaximum = $schema->getMaximum();
        $thisMaximum = $this->getMaximum();

        if (
            !$thatMaximum
            || (
                $thisMaximum
                && $thisMaximum->toFloat() < $thatMaximum->toFloat()
            )
        ) {
            $maximumIsCompatible =  true;
        }

        return $minimumIsCompatible && $maximumIsCompatible;
    }

    private function isStringSchemaCompatible(StringSchema $schema): bool
    {
        $minimumIsCompatible = false;
        $thatMinimumLength = $schema->getMinimumLength();
        $thisMinimumLength = $this->getMinimumLength();
        if (
            !$thatMinimumLength
            || (
                $thisMinimumLength
                && $thisMinimumLength->toInt() > $thatMinimumLength->toInt()
            )
        ) {
            $minimumIsCompatible = true;
        }

        $maximumIsCompatible = false;
        $thatMaximumLength = $schema->getMaximumLength();
        $thisMaximumLength = $this->getMaximumLength();

        if (
            !$thatMaximumLength
            || (
                $thisMaximumLength
                && $thisMaximumLength->toInt() < $thatMaximumLength->toInt()
            )
        ) {
            $maximumIsCompatible =  true;
        }

        return $minimumIsCompatible && $maximumIsCompatible;
    }
}