<?php declare(strict=1);

namespace App\ApiV1Bundle\ApiSpecification\ApiComponents;

use App\ApiV1Bundle\ApiSpecification\ApiComponents\ComponentsSchema\Schema\SchemaIsNullable;
use App\ApiV1Bundle\ApiSpecification\ApiComponents\ComponentsSchema\Schema\SchemaName;

/**
 * The Schema Object allows the definition of input and output data types. These types can be objects, but also
 * primitives and arrays. This object is an extended subset of the JSON Schema Specification Wright Draft 00.
 * http://spec.openapis.org/oas/v3.0.3#schema-object
 */

abstract class Schema
{
    protected SchemaIsNullable $isNullable;
    protected ?SchemaName $name;

    public abstract function toOpenApiSpecification(): array;

    public abstract function setName(SchemaName $name);

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

    public abstract function makeNullable();
}