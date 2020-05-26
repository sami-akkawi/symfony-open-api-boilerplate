<?php declare(strict=1);

namespace App\ApiV1Bundle\ApiSpecification\ApiComponents;

use App\ApiV1Bundle\ApiSpecification\ApiComponents\ComponentsSchema\Schema\SchemaName;

/**
 * The Schema Object allows the definition of input and output data types. These types can be objects, but also
 * primitives and arrays. This object is an extended subset of the JSON Schema Specification Wright Draft 00.
 * https://swagger.io/specification/#schema-object
 */

abstract class Schema
{
    protected ?SchemaName $name;

    public abstract function toOpenApiSpecification(): array;

    public function hasName(): bool
    {
        return (bool)$this->name;
    }

    public function getName(): ?SchemaName
    {
        return $this->name;
    }
}