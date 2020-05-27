<?php declare(strict=1);

namespace App\ApiV1Bundle\ApiSpecification\ApiComponents\ComponentsSchema;

use App\ApiV1Bundle\ApiSpecification\ApiComponents\ComponentsSchema\Schema\SchemaDescription;
use App\ApiV1Bundle\ApiSpecification\ApiComponents\ComponentsSchema\Schema\SchemaExample;
use App\ApiV1Bundle\ApiSpecification\ApiComponents\ComponentsSchema\Schema\SchemaIsNullable;
use App\ApiV1Bundle\ApiSpecification\ApiComponents\ComponentsSchema\Schema\SchemaMaximum;
use App\ApiV1Bundle\ApiSpecification\ApiComponents\ComponentsSchema\Schema\SchemaMinimum;
use App\ApiV1Bundle\ApiSpecification\ApiComponents\ComponentsSchema\Schema\SchemaName;
use App\ApiV1Bundle\ApiSpecification\ApiComponents\ComponentsSchema\Schema\SchemaType;
use App\ApiV1Bundle\ApiSpecification\ApiException\SpecificationException;

final class IntegerSchema extends PrimitiveSchema
{
    private SchemaType $type;
    protected ?SchemaName $name;
    private ?SchemaDescription $description;
    private ?SchemaExample $example;
    private ?SchemaMinimum $minimum;
    private ?SchemaMaximum $maximum;

    private function __construct(
        SchemaType $type,
        ?SchemaName $name = null,
        ?SchemaDescription $description = null,
        ?SchemaExample $example = null,
        ?SchemaMinimum $minimum = null,
        ?SchemaMaximum $maximum = null,
        ?SchemaIsNullable $isNullable = null
    ) {
        $this->type = $type;
        $this->name = $name;
        $this->description = $description;
        $this->example = $example;
        if ($minimum && $maximum && ($minimum->toInt() > $maximum->toInt())) {
            throw SpecificationException::generateMinimumShouldBeLessThanMaximum();
        }
        $this->minimum = $minimum;
        $this->maximum = $maximum;
        $this->isNullable = $isNullable ?? SchemaIsNullable::generateFalse();
    }

    public function setName(SchemaName $name): self
    {
        return new self(
            $this->type,
            $name,
            $this->description,
            $this->example,
            $this->minimum,
            $this->maximum,
            $this->isNullable
        );
    }

    public function setFormat(string $format): self
    {
        return new self(
            $this->type->setFormat($format),
            $this->name,
            $this->description,
            $this->example,
            $this->minimum,
            $this->maximum,
            $this->isNullable
        );
    }

    public function setDescription(string $description): self
    {
        return new self(
            $this->type,
            $this->name,
            SchemaDescription::fromString($description),
            $this->example,
            $this->minimum,
            $this->maximum,
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
            $this->minimum,
            $this->maximum,
            $this->isNullable
        );
    }

    public function setMinimum(int $minimum): self
    {
        return new self(
            $this->type,
            $this->name,
            $this->description,
            $this->example,
            SchemaMinimum::fromInt($minimum),
            $this->maximum,
            $this->isNullable
        );
    }

    public function setMaximum(int $maximum): self
    {
        return new self(
            $this->type,
            $this->name,
            $this->description,
            $this->example,
            $this->minimum,
            SchemaMaximum::fromInt($maximum),
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
            $this->minimum,
            $this->maximum,
            SchemaIsNullable::generateTrue()
        );
    }

    public static function generate(?string $name = null): self
    {
        return new self(SchemaType::generateInteger(), $name ? SchemaName::fromString($name) : null);
    }

    public function toOpenApiSpecification(): array
    {
        $specification = ['type' => $this->type->getType()];
        if ($this->type->hasFormat()) {
            $specification['format'] = $this->type->getFormat();
        }
        if ($this->description) {
            $specification['description'] = $this->description->toString();
        }
        if ($this->example) {
            $specification['example'] = $this->example->toString();
        }
        if ($this->minimum) {
            $specification['minimum'] = $this->minimum->toInt();
        }
        if ($this->maximum) {
            $specification['maximum'] = $this->maximum->toInt();
        }
        if ($this->isNullable()) {
            $specification['nullable'] = true;
        }
        return $specification;
    }
}