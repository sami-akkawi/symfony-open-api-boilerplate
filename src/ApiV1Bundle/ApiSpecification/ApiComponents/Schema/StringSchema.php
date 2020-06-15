<?php declare(strict=1);

namespace App\ApiV1Bundle\ApiSpecification\ApiComponents\Schema;

use App\ApiV1Bundle\ApiSpecification\ApiComponents\Schema\Schema\SchemaDescription;
use App\ApiV1Bundle\ApiSpecification\ApiComponents\Schema\Schema\SchemaExample;
use App\ApiV1Bundle\ApiSpecification\ApiComponents\Schema\Schema\SchemaIsNullable;
use App\ApiV1Bundle\ApiSpecification\ApiComponents\Schema\Schema\SchemaIsRequired;
use App\ApiV1Bundle\ApiSpecification\ApiComponents\Schema\Schema\SchemaName;
use App\ApiV1Bundle\ApiSpecification\ApiComponents\Schema\Schema\SchemaType;

final class StringSchema extends PrimitiveSchema
{
    private SchemaType $type;
    protected ?SchemaName $name;
    private ?SchemaDescription $description;

    private function __construct(
        SchemaType $type,
        SchemaIsRequired $isRequired,
        ?SchemaName $name = null,
        ?SchemaDescription $description = null,
        ?SchemaExample $example = null,
        ?SchemaIsNullable $isNullable = null
    ) {
        $this->type = $type;
        $this->isRequired = $isRequired;
        $this->name = $name;
        $this->description = $description;
        $this->example = $example;
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
            $this->isNullable
        );
    }

    public static function generate(): self
    {
        return new self(SchemaType::generateString(), SchemaIsRequired::generateFalse());
    }

    public function setFormat(string $format): self
    {
        return new self(
            $this->type->setFormat($format),
            $this->isRequired,
            $this->name,
            $this->description,
            $this->example,
            $this->isNullable
        );
    }

    public function setOptions(array $options): self
    {
        return new self(
            $this->type->setEnum($options),
            $this->isRequired,
            $this->name,
            $this->description,
            $this->example,
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
            SchemaIsNullable::generateTrue()
        );
    }

    public function isValueValid($value): array
    {
        if (is_string($value)) {
            return [];
        }
        return [$this->getWrongTypeMessage('string', $value)];
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
            $specification['example'] = $this->example->toAny();
        }
        if ($this->isNullable()) {
            $specification['nullable'] = true;
        }
        return $specification;
    }
}