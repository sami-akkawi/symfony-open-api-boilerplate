<?php declare(strict_types=1);

namespace App\OpenApiSpecification\ApiComponents\Schema;

use App\OpenApiSpecification\ApiComponents\Example;
use App\OpenApiSpecification\ApiComponents\Schema\Schema\SchemaDescription;
use App\OpenApiSpecification\ApiComponents\Schema\Schema\SchemaIsDeprecated;
use App\OpenApiSpecification\ApiComponents\Schema\Schema\SchemaIsNullable;
use App\OpenApiSpecification\ApiComponents\Schema\Schema\SchemaIsRequired;
use App\OpenApiSpecification\ApiComponents\Schema\Schema\SchemaMaximum;
use App\OpenApiSpecification\ApiComponents\Schema\Schema\SchemaMinimum;
use App\OpenApiSpecification\ApiComponents\Schema\Schema\SchemaName;
use App\OpenApiSpecification\ApiComponents\Schema\Schema\SchemaType;
use App\OpenApiSpecification\ApiException\SpecificationException;

final class NumberSchema extends PrimitiveSchema
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
        ?Example $example = null,
        ?SchemaMinimum $minimum = null,
        ?SchemaMaximum $maximum = null,
        ?SchemaIsNullable $isNullable = null,
        ?SchemaIsDeprecated $isDeprecated = null
    ) {
        $this->type = $type;
        $this->isRequired = $isRequired;
        $this->isDeprecated = $isDeprecated ?? SchemaIsDeprecated::generateFalse();
        $this->name = $name;
        $this->description = $description;
        $this->example = $example;
        if ($minimum && $maximum && ($minimum->toFloat() > $maximum->toFloat())) {
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
            $this->isNullable,
            $this->isDeprecated
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
            $this->isNullable,
            $this->isDeprecated
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
            $this->isNullable,
            $this->isDeprecated
        );
    }

    public function unRequire(): self
    {
        return new self(
            $this->type,
            SchemaIsRequired::generateFalse(),
            $this->name,
            $this->description,
            $this->example,
            $this->minimum,
            $this->maximum,
            $this->isNullable,
            $this->isDeprecated
        );
    }

    public function deprecate(): self
    {
        return new self(
            $this->type,
            $this->isRequired,
            $this->name,
            $this->description,
            $this->example,
            $this->minimum,
            $this->maximum,
            $this->isNullable,
            SchemaIsDeprecated::generateTrue()
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
            $this->isNullable,
            $this->isDeprecated
        );
    }

    public function setExample(Example $example): self
    {
        $exception = $this->validateValue($example->toDetailedExample()->getLiteralValue());
        if ($exception) {
            throw $exception;
        }

        return new self(
            $this->type,
            $this->isRequired,
            $this->name,
            $this->description,
            $example,
            $this->minimum,
            $this->maximum,
            $this->isNullable,
            $this->isDeprecated
        );
    }

    public function setMinimum(float $minimum): self
    {
        return new self(
            $this->type,
            $this->isRequired,
            $this->name,
            $this->description,
            $this->example,
            SchemaMinimum::fromFloat($minimum),
            $this->maximum,
            $this->isNullable,
            $this->isDeprecated
        );
    }

    public function setMaximum(float $maximum): self
    {
        return new self(
            $this->type,
            $this->isRequired,
            $this->name,
            $this->description,
            $this->example,
            $this->minimum,
            SchemaMaximum::fromFloat($maximum),
            $this->isNullable,
            $this->isDeprecated
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
            SchemaIsNullable::generateTrue(),
            $this->isDeprecated
        );
    }

    public static function generate(): self
    {
        return new self(SchemaType::generateNumber(), SchemaIsRequired::generateFalse());
    }

    public function isValueValid($value): array
    {
        if (is_float($value)) {
            return [];
        }
        return [$this->getWrongTypeMessage('float', $value)];
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
            $specification['example'] = $this->example->getLiteralValue();
        }
        if ($this->minimum) {
            $specification['minimum'] = $this->minimum->toFloat();
        }
        if ($this->maximum) {
            $specification['maximum'] = $this->maximum->toFloat();
        }
        if ($this->isNullable()) {
            $specification['nullable'] = true;
        }
        return $specification;
    }
}