<?php declare(strict=1);

namespace App\ApiV1Bundle\ApiSpecification\ApiComponents\ComponentsSchema;

use App\ApiV1Bundle\ApiSpecification\ApiComponents\ComponentsSchema\Schema\SchemaAdditionalProperty;
use App\ApiV1Bundle\ApiSpecification\ApiComponents\ComponentsSchema\Schema\SchemaIsNullable;
use App\ApiV1Bundle\ApiSpecification\ApiComponents\ComponentsSchema\Schema\SchemaName;
use App\ApiV1Bundle\ApiSpecification\ApiComponents\Schema;

final class MapSchema extends Schema
{
    private SchemaAdditionalProperty $additionalProperty;

    private function __construct(
        SchemaAdditionalProperty $additionalProperty,
        ?SchemaName $name = null,
        ?SchemaIsNullable $isNullable = null
    ) {
        $this->additionalProperty = $additionalProperty;
        $this->name = $name;
        $this->isNullable = $isNullable ?? SchemaIsNullable::generateFalse();
    }

    public static function generateStringMap(string $name): self
    {
        return new self(SchemaAdditionalProperty::fromStringSchema(StringSchema::generate()), SchemaName::fromString($name));
    }

    public static function fromReferenceSchema(ReferenceSchema $schema): self
    {
        return new self(SchemaAdditionalProperty::fromReferenceSchema($schema));
    }

    public function makeNullable(): self
    {
        return new self(
            $this->additionalProperty,
            $this->name,
            SchemaIsNullable::generateTrue()
        );
    }

    public function toOpenApiSpecification(): array
    {
        $specification =  [
            'type' => 'object',
            'additionalProperties' => $this->additionalProperty->toOpenApiSpecification()
        ];
        if ($this->isNullable()) {
            $specification['nullable'] = true;
        }
        return $specification;
    }

    public function setName(SchemaName $name): self
    {
        return new self($this->additionalProperty, $name, $this->isNullable);
    }
}