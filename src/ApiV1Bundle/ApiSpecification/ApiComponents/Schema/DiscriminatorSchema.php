<?php declare(strict=1);

namespace App\ApiV1Bundle\ApiSpecification\ApiComponents\Schema;

use App\ApiV1Bundle\ApiSpecification\ApiComponents\Schema\Schema\DiscriminatorSchemaType;
use App\ApiV1Bundle\ApiSpecification\ApiComponents\Schema\Schema\SchemaDescription;
use App\ApiV1Bundle\ApiSpecification\ApiComponents\Schema\Schema\SchemaIsNullable;
use App\ApiV1Bundle\ApiSpecification\ApiComponents\Schema\Schema\SchemaIsRequired;
use App\ApiV1Bundle\ApiSpecification\ApiComponents\Schema\Schema\SchemaName;
use App\ApiV1Bundle\ApiSpecification\ApiComponents\Schema;
use App\ApiV1Bundle\ApiSpecification\ApiComponents\Schemas;
use App\ApiV1Bundle\ApiSpecification\ApiException\SpecificationException;
use Symfony\Component\Uid\Uuid;

final class DiscriminatorSchema extends DetailedSchema
{
    protected DiscriminatorSchemaType $type;
    protected Schemas $schemas;
    protected ?SchemaName $name;
    protected ?SchemaDescription $description;

    private function __construct(
        DiscriminatorSchemaType $type,
        SchemaIsRequired $isRequired,
        Schemas $schemas,
        ?SchemaName $name = null,
        ?SchemaDescription $description = null,
        ?SchemaIsNullable $isNullable = null
    )
    {
        $this->type = $type;
        $this->isRequired = $isRequired;
        $this->schemas = $schemas;
        $this->name = $name;
        $this->description = $description;
        $this->isNullable = $isNullable ?? SchemaIsNullable::generateFalse();
    }

    public static function generateAnyOf(): self
    {
        return new self(DiscriminatorSchemaType::generateAnyOf(), SchemaIsRequired::generateFalse(), Schemas::generate());
    }

    public static function generateAllOf(): self
    {
        return new self(DiscriminatorSchemaType::generateAllOf(), SchemaIsRequired::generateFalse(), Schemas::generate());
    }

    public static function generateOneOf(): self
    {
        return new self(DiscriminatorSchemaType::generateOneOf(), SchemaIsRequired::generateFalse(), Schemas::generate());
    }

    public function addSchema(Schema $schema): self
    {
        return new self(
            $this->type,
            $this->isRequired,
            $this->schemas->addSchema($schema->setName(Uuid::v4()->toRfc4122())),
            $this->name,
            $this->description,
            $this->isNullable
        );
    }

    public function setDescription(string $description): self
    {
        return new self(
            $this->type,
            $this->isRequired,
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
            $this->isRequired,
            $this->schemas,
            $this->name,
            $this->description,
            SchemaIsNullable::generateTrue()
        );
    }

    public function require(): self
    {
        return new self(
            $this->type,
            SchemaIsRequired::generateTrue(),
            $this->schemas,
            $this->name,
            $this->description,
            $this->isNullable
        );
    }

    public function setName(string $name): self
    {
        return new self($this->type, $this->isRequired, $this->schemas, SchemaName::fromString($name), $this->description);
    }

    public function toOpenApiSpecification(): array
    {
        $specification = [];
        if (!$this->schemas->isDefined()) {
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
}