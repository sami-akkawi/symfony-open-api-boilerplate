<?php declare(strict=1);

namespace App\ApiV1Bundle\ApiSpecification\ApiComponents\Schema;

use App\ApiV1Bundle\ApiSpecification\ApiComponents\Schema\Schema\SchemaDescription;
use App\ApiV1Bundle\ApiSpecification\ApiComponents\Schema\Schema\SchemaIsNullable;
use App\ApiV1Bundle\ApiSpecification\ApiComponents\Schema\Schema\SchemaIsRequired;
use App\ApiV1Bundle\ApiSpecification\ApiComponents\Schema\Schema\SchemaName;
use App\ApiV1Bundle\ApiSpecification\ApiComponents\Schema\Schema\SchemaType;
use App\ApiV1Bundle\ApiSpecification\ApiComponents\Schemas;
use App\ApiV1Bundle\ApiSpecification\ApiException\SpecificationException;

final class ObjectSchema extends DetailedSchema
{
    protected ?SchemaName $name;
    private SchemaType $type;
    private Schemas $properties;
    private ?SchemaDescription $description;

    private function __construct(
        Schemas $properties,
        SchemaIsRequired $isRequired,
        ?SchemaName $name = null,
        ?SchemaDescription $description = null,
        ?SchemaIsNullable $isNullable = null
    ) {
        if (!$properties->isDefined()) {
            throw SpecificationException::generateObjectSchemaNeedsProperties($name ? $name->toString() : 'no_name');
        }
        $this->name = $name;
        $this->isRequired = $isRequired;
        $this->type = SchemaType::generateObject();
        $this->properties = $properties;
        $this->description = $description;
        $this->isNullable = $isNullable ?? SchemaIsNullable::generateFalse();
    }

    public function setName(string $name): self
    {
        return new self(
            $this->properties,
            $this->isRequired,
            SchemaName::fromString($name),
            $this->description,
            $this->isNullable
        );
    }

    public function hasProperty(string $propertyName): bool
    {
        return $this->properties->hasSchema(SchemaName::fromString($propertyName));
    }

    public function require(): self
    {
        return new self(
            $this->properties,
            SchemaIsRequired::generateTrue(),
            $this->name,
            $this->description,
            $this->isNullable
        );
    }

    public static function generate(Schemas $properties): self
    {
        return new self($properties, SchemaIsRequired::generateFalse());
    }

    public function setDescription(string $description): self
    {
        return new self(
            $this->properties,
            $this->isRequired,
            $this->name,
            SchemaDescription::fromString($description),
            $this->isNullable
        );
    }

    public function makeNullable(): self
    {
        return new self(
            $this->properties,
            $this->isRequired,
            $this->name,
            $this->description,
            SchemaIsNullable::generateTrue()
        );
    }

    private function getRequiredProperties(): array
    {
        return $this->properties->getRequiredSchemaNames();
    }

    public function toOpenApiSpecification(): array
    {
        $specification = [
            'type' => $this->type->getType(),
            'properties' => $this->properties->toOpenApiSpecification()
        ];
        $requiredProperties = $this->getRequiredProperties();
        if (!empty($requiredProperties)) {
            $specification['required'] = $requiredProperties;
        }
        if ($this->description) {
            $specification['description'] = $this->description->toString();
        }
        if ($this->isNullable()) {
            $specification['nullable'] = true;
        }
        return $specification;
    }
}