<?php declare(strict=1);

namespace App\ApiV1Bundle\ApiSpecification\ApiComponents\Schema;

use App\ApiV1Bundle\ApiSpecification\ApiComponents\Schema\Schema\SchemaDescription;
use App\ApiV1Bundle\ApiSpecification\ApiComponents\Schema\Schema\SchemaExample;
use App\ApiV1Bundle\ApiSpecification\ApiComponents\Schema\Schema\SchemaIsNullable;
use App\ApiV1Bundle\ApiSpecification\ApiComponents\Schema\Schema\SchemaIsRequired;
use App\ApiV1Bundle\ApiSpecification\ApiComponents\Schema\Schema\SchemaMaximum;
use App\ApiV1Bundle\ApiSpecification\ApiComponents\Schema\Schema\SchemaMinimum;
use App\ApiV1Bundle\ApiSpecification\ApiComponents\Schema\Schema\SchemaName;
use App\ApiV1Bundle\ApiSpecification\ApiComponents\Schema\Schema\SchemaType;
use App\ApiV1Bundle\ApiSpecification\ApiException\SpecificationException;

final class IntegerSchema extends PrimitiveSchema
{
    private SchemaType $type;
    protected ?SchemaName $name;
    private ?SchemaDescription $description;
    private ?SchemaMinimum $minimum;
    private ?SchemaMaximum $maximum;

    private function __construct(
        SchemaType $type,
        SchemaIsRequired $isRequired,
        ?SchemaName $name = null,
        ?SchemaDescription $description = null,
        ?SchemaExample $example = null,
        ?SchemaMinimum $minimum = null,
        ?SchemaMaximum $maximum = null,
        ?SchemaIsNullable $isNullable = null
    ) {
        $this->type = $type;
        $this->isRequired = $isRequired;
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

    public function setName(string $name): self
    {
        return new self(
            $this->type,
            $this->isRequired,
            SchemaName::fromString($name),
            $this->description,
            $this->example,
            $this->minimum,
            $this->maximum,
            $this->isNullable
        );
    }

    public function require(): self
    {
        return new self(
            $this->type,
            SchemaIsRequired::generateTrue(),
            $this->name,
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
            $this->isRequired,
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
            $this->isRequired,
            $this->name,
            SchemaDescription::fromString($description),
            $this->example,
            $this->minimum,
            $this->maximum,
            $this->isNullable
        );
    }

    public function setExample($example): self
    {
        $exception = $this->validateValue($example);
        if ($exception) {
            throw $exception;
        }

        return new self(
            $this->type,
            $this->isRequired,
            $this->name,
            $this->description,
            SchemaExample::fromAny($example),
            $this->minimum,
            $this->maximum,
            $this->isNullable
        );
    }

    public function setMinimum(int $minimum): self
    {
        return new self(
            $this->type,
            $this->isRequired,
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
            $this->isRequired,
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
            $this->isRequired,
            $this->name,
            $this->description,
            $this->example,
            $this->minimum,
            $this->maximum,
            SchemaIsNullable::generateTrue()
        );
    }

    public static function generate(): self
    {
        return new self(SchemaType::generateInteger(), SchemaIsRequired::generateFalse());
    }

    public function isValueValid($value): array
    {
        if (is_int($value)) {
            return [];
        }
        return [$this->getWrongTypeMessage('integer', $value)];
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
            $specification['example'] = $this->example->toAny();
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