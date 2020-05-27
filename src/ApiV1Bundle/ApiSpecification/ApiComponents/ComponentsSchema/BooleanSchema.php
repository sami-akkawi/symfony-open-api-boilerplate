<?php declare(strict=1);

namespace App\ApiV1Bundle\ApiSpecification\ApiComponents\ComponentsSchema;

use App\ApiV1Bundle\ApiSpecification\ApiComponents\ComponentsSchema\Schema\SchemaDescription;
use App\ApiV1Bundle\ApiSpecification\ApiComponents\ComponentsSchema\Schema\SchemaExample;
use App\ApiV1Bundle\ApiSpecification\ApiComponents\ComponentsSchema\Schema\SchemaIsNullable;
use App\ApiV1Bundle\ApiSpecification\ApiComponents\ComponentsSchema\Schema\SchemaName;
use App\ApiV1Bundle\ApiSpecification\ApiComponents\ComponentsSchema\Schema\SchemaType;

final class BooleanSchema extends PrimitiveSchema
{
    private SchemaType $type;
    protected ?SchemaName $name;
    private ?SchemaDescription $description;
    private ?SchemaExample $example;

    private function __construct(
        SchemaType $type,
        ?SchemaName $name = null,
        ?SchemaDescription $description = null,
        ?SchemaExample $example = null,
        ?SchemaIsNullable $isNullable = null
    ) {
        $this->type = $type;
        $this->name = $name;
        $this->description = $description;
        $this->example = $example;
        $this->isNullable = $isNullable ?? SchemaIsNullable::generateFalse();
    }

    public function setName(SchemaName $name): self
    {
        return new self(
            $this->type,
            $name,
            $this->description,
            $this->example,
            $this->isNullable
        );
    }

    public static function generate(?string $name = null): self
    {
        return new self(SchemaType::generateBoolean(), $name ? SchemaName::fromString($name) : null);
    }

    public function setDescription(string $description): self
    {
        return new self(
            $this->type,
            $this->name,
            SchemaDescription::fromString($description),
            $this->example,
            $this->isNullable
        );
    }

    public function setExample(string $example): self
    {
        return new self(
            $this->type,
            $this->name,
            $this->description,
            SchemaExample::fromString($example),
            $this->isNullable
        );
    }

    public function makeNullable(): self
    {
        return new self(
            $this->type,
            $this->name,
            $this->description,
            $this->example,
            SchemaIsNullable::generateTrue()
        );
    }

    public function toOpenApiSpecification(): array
    {
        $specification = ['type' => $this->type->getType()];
        if ($this->description) {
            $specification['description'] = $this->description->toString();
        }
        if ($this->example) {
            $specification['example'] = $this->example->toString();
        }
        if ($this->isNullable()) {
            $specification['nullable'] = true;
        }
        return $specification;
    }
}