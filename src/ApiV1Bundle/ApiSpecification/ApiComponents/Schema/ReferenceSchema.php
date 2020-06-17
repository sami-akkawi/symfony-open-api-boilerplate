<?php declare(strict=1);

namespace App\ApiV1Bundle\ApiSpecification\ApiComponents\Schema;

use App\ApiV1Bundle\ApiSpecification\ApiComponents\Schema\Schema\SchemaIsNullable;
use App\ApiV1Bundle\ApiSpecification\ApiComponents\Schema\Schema\SchemaIsRequired;
use App\ApiV1Bundle\ApiSpecification\ApiComponents\Reference;
use App\ApiV1Bundle\ApiSpecification\ApiComponents\Schema;
use App\ApiV1Bundle\ApiSpecification\ApiComponents\Schema\Schema\SchemaName;
use App\ApiV1Bundle\ApiSpecification\ApiException\SpecificationException;

/**
 * The Reference Object is defined by JSON Reference and follows the same structure, behavior and rules.
 * http://spec.openapis.org/oas/v3.0.3#reference-object
 */

final class ReferenceSchema extends Schema
{
    private Reference $reference;
    protected ?SchemaName $name;
    private DetailedSchema $schema;

    private function __construct(
        Reference $reference,
        SchemaIsRequired $isRequired,
        DetailedSchema $schema,
        ?SchemaName $name = null
    ) {
        $this->reference = $reference;
        $this->isRequired = $isRequired;
        $this->name = $name;
        $this->schema = $schema;
        $this->isNullable = SchemaIsNullable::generateFalse();
    }

    public function isValueValid($value): array
    {
        return $this->schema->isValueValid($value);
    }

    public function makeNullable()
    {
        throw SpecificationException::generateReferenceSiblingsAreIgnored();
    }

    public function setName(string $name): self
    {
        return new self($this->reference, $this->isRequired, $this->schema, SchemaName::fromString($name));
    }

    public static function generate(string $objectName, DetailedSchema $schema): self
    {
        return new self(Reference::generateSchemaReference($objectName), SchemaIsRequired::generateFalse(), $schema);
    }

    public function require(): self
    {
        return new self($this->reference, SchemaIsRequired::generateTrue(), $this->schema, $this->name);
    }

    public function unRequire(): self
    {
        return new self($this->reference, SchemaIsRequired::generateFalse(), $this->schema, $this->name);
    }

    public function deprecate(): self
    {
        throw SpecificationException::generateReferenceSiblingsAreIgnored();
    }

    public function setExample($example): self
    {
        throw SpecificationException::generateReferenceSiblingsAreIgnored();
    }

    public function toOpenApiSpecification(): array
    {
        return $this->reference->toOpenApiSpecification();
    }

    public function toDetailedSchema(): DetailedSchema
    {
        return $this->schema;
    }
}