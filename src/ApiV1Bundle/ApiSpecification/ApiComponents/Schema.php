<?php declare(strict=1);

namespace App\ApiV1Bundle\ApiSpecification\ApiComponents;

use App\ApiV1Bundle\ApiSpecification\ApiComponents\Schema\DetailedSchema;
use App\ApiV1Bundle\ApiSpecification\ApiComponents\Schema\Schema\SchemaIsNullable;
use App\ApiV1Bundle\ApiSpecification\ApiComponents\Schema\Schema\SchemaIsRequired;
use App\ApiV1Bundle\ApiSpecification\ApiComponents\Schema\Schema\SchemaName;
use App\ApiV1Bundle\ApiSpecification\ApiException\SpecificationException;

/**
 * The Schema Object allows the definition of input and output data types. These types can be objects, but also
 * primitives and arrays. This object is an extended subset of the JSON Schema Specification Wright Draft 00.
 * http://spec.openapis.org/oas/v3.0.3#schema-object
 */

abstract class Schema
{
    protected SchemaIsNullable $isNullable;
    protected ?SchemaName $name;
    protected SchemaIsRequired $isRequired;
    // todo: protected ?Example $example;

    public abstract function toOpenApiSpecification(): array;

    public abstract function setName(string $name);

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

    public abstract function require();

    public abstract function makeNullable();

    public abstract function toDetailedSchema(): DetailedSchema;

    public abstract function isValueValid($value): array;

    public function validateValue($value): ?SpecificationException
    {
        $errors = $this->isValueValid($value);
        if ($errors) {
            return new SpecificationException($this->getKeyErrorAndValues($errors));
        }
        return null;
    }

    private function getKeyErrorAndValues(array $errors): string
    {
        $string = '';
        foreach ($errors as $key => $error) {
            $string .= "$key: ";
            if (is_array($error)) {
                $string .= $this->getKeyErrorAndValues($error);
            } else {
                $string .= $error;
            }
            $string .= PHP_EOL;
        }

        return $string;
    }
}