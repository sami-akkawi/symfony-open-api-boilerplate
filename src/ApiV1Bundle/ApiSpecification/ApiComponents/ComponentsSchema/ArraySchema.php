<?php declare(strict=1);

namespace App\ApiV1Bundle\ApiSpecification\ApiComponents\ComponentsSchema;

use App\ApiV1Bundle\ApiSpecification\ApiComponents\Schema;
use App\ApiV1Bundle\ApiSpecification\ApiComponents\ComponentsSchema\Schema\SchemaDescription;
use App\ApiV1Bundle\ApiSpecification\ApiComponents\ComponentsSchema\Schema\SchemaName;
use App\ApiV1Bundle\ApiSpecification\ApiComponents\ComponentsSchema\Schema\SchemaType;

final class ArraySchema extends Schema
{
    private Schema $itemType;
    protected ?SchemaName $name;
    private bool $itemsAreUnique;
    private SchemaType $type;
    private ?SchemaDescription $description;

    private function __construct(
        Schema $itemType,
        ?SchemaName $name,
        bool $itemsAreUnique = true,
        ?SchemaDescription $description = null
    ) {
        $this->itemType = $itemType;
        $this->name = $name;
        $this->itemsAreUnique = $itemsAreUnique;
        $this->type = SchemaType::generateArray();
        $this->description = $description;
    }

    public static function generateWithUniqueValues(Schema $itemType, ?string $name = null): self
    {
        return new self(
            $itemType,
            $name ? SchemaName::fromString($name) : null
        );
    }

    public static function generateWithoutUniqueValues(Schema $itemType, ?string $name = null): self
    {
        return new self(
            $itemType,
            $name ? SchemaName::fromString($name) : null,
            false
        );
    }

    public function setDescription(string $description): self
    {
        return new self(
            $this->itemType,
            $this->name,
            $this->itemsAreUnique,
            SchemaDescription::fromString($description)
        );
    }

    public function toOpenApiSpecification(): array
    {
        $specification = [
            'type' => $this->type->getType(),
            'items' => $this->itemType->toOpenApiSpecification()
        ];
        if ($this->itemsAreUnique) {
            $specification['uniqueItems'] = true;
        }
        if ($this->description) {
            $specification['description'] = $this->description->toString();
        }
        return $specification;
    }
}