<?php declare(strict_types=1);

namespace App\OpenApiSpecification\ApiComponents;

use App\OpenApiSpecification\ApiComponents\ComponentsSchema\Schema;
use App\OpenApiSpecification\ApiComponents\ComponentsSchema\Schema\SchemaIsDeprecated;
use App\OpenApiSpecification\ApiComponents\ComponentsSchema\Schema\SchemaIsNullable;
use App\OpenApiSpecification\ApiComponents\ComponentsSchema\Schema\SchemaIsRequired;
use App\OpenApiSpecification\ApiComponents\ComponentsSchema\Schema\SchemaName;
use App\OpenApiSpecification\ApiComponents\ComponentsSchema\Schema\SchemaType;
use App\OpenApiSpecification\ApiException\SpecificationException;

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

    protected abstract function getValueFromTrimmedCastedString(string $value);
}