<?php declare(strict=1);

namespace App\ApiV1Bundle\ApiSpecification\ApiComponents\ComponentsSchema;

use App\ApiV1Bundle\ApiSpecification\ApiComponents\ComponentsSchema\Schema\SchemaDescription;
use App\ApiV1Bundle\ApiSpecification\ApiComponents\ComponentsSchema\Schema\SchemaExample;
use App\ApiV1Bundle\ApiSpecification\ApiComponents\ComponentsSchema\Schema\SchemaName;
use App\ApiV1Bundle\ApiSpecification\ApiComponents\ComponentsSchema\Schema\SchemaType;

final class StringSchema extends PrimitiveSchema
{
    private SchemaType $type;
    protected ?SchemaName $name;
    private ?SchemaDescription $description;
    private ?SchemaExample $example;

    private function __construct(
        SchemaType $type,
        ?SchemaName $name = null,
        ?SchemaDescription $description = null,
        ?SchemaExample $example = null
    ) {
        $this->type = $type;
        $this->name = $name;
        $this->description = $description;
        $this->example = $example;
    }

    public static function generate(?string $name = null): self
    {
        return new self(SchemaType::generateString(), $name ? SchemaName::fromString($name) : null);
    }

    public function setFormat(string $format): self
    {
        return new self(
            $this->type->setFormat($format),
            $this->name,
            $this->description,
            $this->example
        );
    }

    public function setOptions(array $options): self
    {
        return new self(
            $this->type->setEnum($options),
            $this->name,
            $this->description,
            $this->example
        );
    }

    public function setDescription(string $description): self
    {
        return new self(
            $this->type,
            $this->name,
            SchemaDescription::fromString($description),
            $this->example
        );
    }

    public function setExample(string $example): self
    {
        return new self(
            $this->type,
            $this->name,
            $this->description,
            SchemaExample::fromString($example)
        );
    }

    public function toOpenApiSpecification(): array
    {
        $specification = ['type' => $this->type->getType()];
        if ($this->type->hasFormat()) {
            $specification['format'] = $this->type->getFormat();
        }
        if ($this->type->isEnum()) {
            $specification['enum'] = $this->type->getEnum();
        }
        if ($this->description) {
            $specification['description'] = $this->description->toString();
        }
        if ($this->example) {
            $specification['example'] = $this->example->toString();
        }
        return $specification;
    }
}