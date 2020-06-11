<?php declare(strict=1);

namespace App\ApiV1Bundle\ApiSpecification\ApiComponents\Schema;

use App\ApiV1Bundle\ApiSpecification\ApiComponents\Schema\Schema\SchemaAdditionalProperty;
use App\ApiV1Bundle\ApiSpecification\ApiComponents\Schema\Schema\SchemaIsNullable;
use App\ApiV1Bundle\ApiSpecification\ApiComponents\Schema\Schema\SchemaIsRequired;
use App\ApiV1Bundle\ApiSpecification\ApiComponents\Schema\Schema\SchemaName;

final class MapSchema extends DetailedSchema
{
    private SchemaAdditionalProperty $additionalProperty;

    private function __construct(
        SchemaAdditionalProperty $additionalProperty,
        SchemaIsRequired $isRequired,
        ?SchemaName $name = null,
        ?SchemaIsNullable $isNullable = null
    ) {
        $this->additionalProperty = $additionalProperty;
        $this->isRequired = $isRequired;
        $this->name = $name;
        $this->isNullable = $isNullable ?? SchemaIsNullable::generateFalse();
    }

    public static function generateStringMap(): self
    {
        return new self(SchemaAdditionalProperty::fromStringSchema(StringSchema::generate()), SchemaIsRequired::generateFalse());
    }

    public static function fromReferenceSchema(ReferenceSchema $schema): self
    {
        return new self(SchemaAdditionalProperty::fromReferenceSchema($schema), SchemaIsRequired::generateFalse());
    }

    public function makeNullable(): self
    {
        return new self(
            $this->additionalProperty,
            $this->isRequired,
            $this->name,
            SchemaIsNullable::generateTrue()
        );
    }

    public function require(): self
    {
        return new self(
            $this->additionalProperty,
            SchemaIsRequired::generateTrue(),
            $this->name,
            $this->isNullable
        );
    }

    public function setName(string $name): self
    {
        return new self($this->additionalProperty, $this->isRequired, SchemaName::fromString($name), $this->isNullable);
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
}