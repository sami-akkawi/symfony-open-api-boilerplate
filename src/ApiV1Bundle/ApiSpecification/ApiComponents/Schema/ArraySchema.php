<?php declare(strict=1);

namespace App\ApiV1Bundle\ApiSpecification\ApiComponents\Schema;

use App\ApiV1Bundle\ApiSpecification\ApiComponents\Schema\Schema\SchemaIsNullable;
use App\ApiV1Bundle\ApiSpecification\ApiComponents\Schema\Schema\SchemaItemsAreUnique;
use App\ApiV1Bundle\ApiSpecification\ApiComponents\Schema;
use App\ApiV1Bundle\ApiSpecification\ApiComponents\Schema\Schema\SchemaDescription;
use App\ApiV1Bundle\ApiSpecification\ApiComponents\Schema\Schema\SchemaIsRequired;
use App\ApiV1Bundle\ApiSpecification\ApiComponents\Schema\Schema\SchemaName;
use App\ApiV1Bundle\ApiSpecification\ApiComponents\Schema\Schema\SchemaType;

final class ArraySchema extends DetailedSchema
{
    private Schema $itemType;
    protected ?SchemaName $name;
    private SchemaItemsAreUnique $itemsAreUnique;
    private SchemaType $type;
    private ?SchemaDescription $description;

    private function __construct(
        Schema $itemType,
        SchemaIsRequired $isRequired,
        ?SchemaName $name = null,
        ?SchemaItemsAreUnique $itemsAreUnique = null,
        ?SchemaDescription $description = null,
        ?SchemaIsNullable $isNullable = null
    ) {
        $this->itemType = $itemType;
        $this->isRequired = $isRequired;
        $this->name = $name;
        $this->itemsAreUnique = $itemsAreUnique ?? SchemaItemsAreUnique::generateFalse();
        $this->type = SchemaType::generateArray();
        $this->description = $description;
        $this->isNullable = $isNullable ?? SchemaIsNullable::generateFalse();
    }

    public function setName(string $name): self
    {
        return new self(
            $this->itemType,
            $this->isRequired,
            SchemaName::fromString($name),
            $this->itemsAreUnique,
            $this->description,
            $this->isNullable
        );
    }

    public function makeValuesUnique(): self
    {
        return new self(
            $this->itemType,
            $this->isRequired,
            $this->name,
            SchemaItemsAreUnique::generateTrue(),
            $this->description,
            $this->isNullable
        );
    }

    public static function generate(Schema $itemType): self
    {
        return new self($itemType, SchemaIsRequired::generateFalse());
    }

    public function setDescription(string $description): self
    {
        return new self(
            $this->itemType,
            $this->isRequired,
            $this->name,
            $this->itemsAreUnique,
            SchemaDescription::fromString($description),
            $this->isNullable
        );
    }

    public function makeNullable(): self
    {
        return new self(
            $this->itemType,
            $this->isRequired,
            $this->name,
            $this->itemsAreUnique,
            $this->description,
            SchemaIsNullable::generateTrue()
        );
    }

    public function require(): self
    {
        return new self(
            $this->itemType,
            SchemaIsRequired::generateTrue(),
            $this->name,
            $this->itemsAreUnique,
            $this->description,
            $this->isNullable
        );
    }

    public function toOpenApiSpecification(): array
    {
        $specification = [
            'type' => $this->type->getType(),
            'items' => $this->itemType->toOpenApiSpecification()
        ];
        if ($this->itemsAreUnique->toBool()) {
            $specification['uniqueItems'] = true;
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