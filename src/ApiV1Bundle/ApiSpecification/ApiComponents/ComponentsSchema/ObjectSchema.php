<?php declare(strict=1);

namespace App\ApiV1Bundle\ApiSpecification\ApiComponents\ComponentsSchema;

use App\ApiV1Bundle\ApiSpecification\ApiComponents\ComponentsSchema\Schema\SchemaDescription;
use App\ApiV1Bundle\ApiSpecification\ApiComponents\ComponentsSchema\Schema\SchemaName;
use App\ApiV1Bundle\ApiSpecification\ApiComponents\ComponentsSchema\Schema\SchemaType;
use App\ApiV1Bundle\ApiSpecification\ApiComponents\Schema;
use App\ApiV1Bundle\ApiSpecification\ApiComponents\Schemas;
use App\ApiV1Bundle\ApiSpecification\ApiException\SpecificationException;

final class ObjectSchema extends Schema
{
    protected ?SchemaName $name;
    private SchemaType $type;
    private Schemas $properties;
    private ?SchemaDescription $description;

    private function __construct(Schemas $properties, ?SchemaName $name = null, ?SchemaDescription $description = null)
    {
        if (!$properties->hasValues()) {
            throw SpecificationException::generateObjectSchemaNeedsProperties($name ? $name->toString() : 'no_name');
        }
        $this->name = $name;
        $this->type = SchemaType::generateObject();
        $this->properties = $properties;
        $this->description = $description;
    }

    public static function generate(Schemas $properties, ?string $name = null): self
    {
        return new self($properties, $name ? SchemaName::fromString($name) : null);
    }

    public function setDescription(string $description): self
    {
        return new self(
            $this->properties,
            $this->name,
            SchemaDescription::fromString($description)
        );
    }

    public function toOpenApiSpecification(): array
    {
        $specification = [
            'type' => $this->type->getType(),
            'properties' => $this->properties->toOpenApiSpecification()
        ];
        if ($this->description) {
            $specification['description'] = $this->description->toString();
        }
        return $specification;
    }
}