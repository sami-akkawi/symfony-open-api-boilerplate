<?php declare(strict=1);

namespace App\ApiV1Bundle\ApiSpecification\ApiComponents\ComponentsSchema;

use App\ApiV1Bundle\ApiSpecification\ApiComponents\Reference;
use App\ApiV1Bundle\ApiSpecification\ApiComponents\Schema;
use App\ApiV1Bundle\ApiSpecification\ApiComponents\ComponentsSchema\Schema\SchemaName;

/**
 * The Reference Object is defined by JSON Reference and follows the same structure, behavior and rules.
 * http://spec.openapis.org/oas/v3.0.3#reference-object
 */

final class ReferenceSchema extends Schema
{
    private Reference $reference;
    protected ?SchemaName $name;

    private function __construct(Reference $reference, ?SchemaName $name = null)
    {
        $this->reference = $reference;
        $this->name = $name;
    }

    public static function generateWithName(string $objectName, string $name): self
    {
        return new self(
            Reference::generateSchemaReference($objectName),
            SchemaName::fromString($name)
        );
    }

    public static function generateWithNoName(string $objectName): self
    {
        return new self(Reference::generateSchemaReference($objectName));
    }

    public function toOpenApiSpecification(): array
    {
        return $this->reference->toOpenApiSpecification();
    }
}