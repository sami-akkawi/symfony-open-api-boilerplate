<?php declare(strict_types=1);

namespace App\OpenApiSpecification\ApiComponents\ComponentsSchema;

use App\OpenApiSpecification\ApiComponents\ComponentsSchema\Schema\SchemaIsNullable;
use App\OpenApiSpecification\ApiComponents\ComponentsSchema\Schema\SchemaIsRequired;
use App\OpenApiSpecification\ApiComponents\Reference;
use App\OpenApiSpecification\ApiComponents\ComponentsSchema;
use App\OpenApiSpecification\ApiComponents\ComponentsSchema\Schema\SchemaName;
use App\OpenApiSpecification\ApiComponents\ComponentsSchema\Schema\SchemaType;
use App\OpenApiSpecification\ApiException\SpecificationException;

/**
 * The Reference Object is defined by JSON Reference and follows the same structure, behavior and rules.
 * http://spec.openapis.org/oas/v3.0.3#reference-object
 */

final class ReferenceSchema extends ComponentsSchema
{
    private Reference $reference;
    protected ?SchemaName $name;
    private Schema $schema;

    private function __construct(
        Reference $reference,
        SchemaIsRequired $isRequired,
        Schema $schema,
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

    public static function generate(string $objectName, Schema $schema): self
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

    public function toSchema(): Schema
    {
        return $this->schema;
    }

    public function getValueFromCastedString(string $value)
    {
        return $this->schema->getValueFromCastedString($value);
    }

    protected function getValueFromTrimmedCastedString(string $value)
    {
        return $this->schema->getValueFromCastedString($value);
    }

    public function getType(): SchemaType
    {
        return $this->schema->getType();
    }
}