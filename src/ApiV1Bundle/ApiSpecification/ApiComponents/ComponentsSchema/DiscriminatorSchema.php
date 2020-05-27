<?php declare(strict=1);

namespace App\ApiV1Bundle\ApiSpecification\ApiComponents\ComponentsSchema;

use App\ApiV1Bundle\ApiSpecification\ApiComponents\ComponentsSchema\Schema\DiscriminatorSchemaType;
use App\ApiV1Bundle\ApiSpecification\ApiComponents\ComponentsSchema\Schema\SchemaDescription;
use App\ApiV1Bundle\ApiSpecification\ApiComponents\ComponentsSchema\Schema\SchemaIsNullable;
use App\ApiV1Bundle\ApiSpecification\ApiComponents\ComponentsSchema\Schema\SchemaName;
use App\ApiV1Bundle\ApiSpecification\ApiComponents\Schema;
use App\ApiV1Bundle\ApiSpecification\ApiComponents\Schemas;
use App\ApiV1Bundle\ApiSpecification\ApiException\SpecificationException;
use Ramsey\Uuid\Uuid;

final class DiscriminatorSchema extends Schema
{
    protected DiscriminatorSchemaType $type;
    protected Schemas $schemas;
    protected ?SchemaName $name;
    protected ?SchemaDescription $description;

    private function __construct(
        DiscriminatorSchemaType $type,
        Schemas $schemas,
        SchemaName $name,
        ?SchemaDescription $description = null,
        ?SchemaIsNullable $isNullable = null
    )
    {
        $this->type = $type;
        $this->schemas = $schemas;
        $this->name = $name;
        $this->description = $description;
        $this->isNullable = $isNullable ?? SchemaIsNullable::generateFalse();
    }

    public static function generateAnyOf(string $name): self
    {
        return new self(DiscriminatorSchemaType::generateAnyOf(), Schemas::generate(), SchemaName::fromString($name));
    }

    public static function generateAllOf(string $name): self
    {
        return new self(DiscriminatorSchemaType::generateAllOf(), Schemas::generate(), SchemaName::fromString($name));
    }

    public static function generateOneOf(string $name): self
    {
        return new self(DiscriminatorSchemaType::generateOneOf(), Schemas::generate(), SchemaName::fromString($name));
    }

    public function addSchema(Schema $schema): self
    {
        return new self(
            $this->type,
            $this->schemas->addSchema($schema->setName(SchemaName::fromString(Uuid::uuid4()->toString()))),
            $this->name,
            $this->description,
            $this->isNullable
        );
    }

    public function setDescription(string $description): self
    {
        return new self(
            $this->type,
            $this->schemas,
            $this->name,
            SchemaDescription::fromString($description),
            $this->isNullable
        );
    }

    public function makeNullable(): self
    {
        return new self(
            $this->type,
            $this->schemas,
            $this->name,
            $this->description,
            SchemaIsNullable::generateTrue()
        );
    }

    public function toOpenApiSpecification(): array
    {
        $specification = [];
        if (!$this->schemas->hasValues()) {
            throw SpecificationException::generateSchemasMustBeDefined();
        }
        if ($this->description) {
            $specification['description'] = $this->description->toString();
        }
        if ($this->isNullable()) {
            $specification['nullable'] = true;
        }
        $specification[$this->type->toString()] = array_values($this->schemas->toOpenApiSpecification());

        return $specification;
    }

    public function setName(SchemaName $name): self
    {
        return new self($this->type, $this->schemas, $name, $this->description);
    }
}