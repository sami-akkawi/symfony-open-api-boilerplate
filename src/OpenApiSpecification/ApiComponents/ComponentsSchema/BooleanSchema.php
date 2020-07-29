<?php declare(strict_types=1);

namespace App\OpenApiSpecification\ApiComponents\ComponentsSchema;

use App\Message\FieldMessage;
use App\OpenApiSpecification\ApiComponents\ComponentsExample;
use App\OpenApiSpecification\ApiComponents\ComponentsSchema\Schema\SchemaDescription;
use App\OpenApiSpecification\ApiComponents\ComponentsSchema\Schema\SchemaIsDeprecated;
use App\OpenApiSpecification\ApiComponents\ComponentsSchema\Schema\SchemaIsNullable;
use App\OpenApiSpecification\ApiComponents\ComponentsSchema\Schema\SchemaIsRequired;
use App\OpenApiSpecification\ApiComponents\ComponentsSchema\Schema\SchemaName;
use App\OpenApiSpecification\ApiComponents\ComponentsSchema\Schema\SchemaType;

final class BooleanSchema extends PrimitiveSchema
{
    private ?SchemaDescription $description;

    private function __construct(
        SchemaType $type,
        SchemaIsRequired $isRequired,
        ?SchemaName $name = null,
        ?SchemaDescription $description = null,
        ?ComponentsExample $example = null,
        ?SchemaIsNullable $isNullable = null,
        ?SchemaIsDeprecated $isDeprecated = null
    ) {
        $this->type = $type;
        $this->isRequired = $isRequired;
        $this->isDeprecated = $isDeprecated ?? SchemaIsDeprecated::generateFalse();
        $this->name = $name;
        $this->description = $description;
        $this->example = $example;
        $this->isNullable = $isNullable ?? SchemaIsNullable::generateFalse();
    }

    public function setName(string $name): self
    {
        $this->name = SchemaName::fromString($name);
        return $this;
    }

    public static function generate(): self
    {
        return new self(SchemaType::generateBoolean(), SchemaIsRequired::generateFalse());
    }

    public function setDescription(string $description): self
    {
        $this->description = SchemaDescription::fromString($description);
        return $this;
    }

    public function makeNullable(): self
    {
        $this->isNullable = SchemaIsNullable::generateTrue();
        return $this;
    }

    public function require(): self
    {
        $this->isRequired = SchemaIsRequired::generateTrue();
        return $this;
    }

    public function unRequire(): self
    {
        $this->isRequired = SchemaIsRequired::generateFalse();
        return $this;
    }

    public function deprecate(): self
    {
        $this->isDeprecated = SchemaIsDeprecated::generateTrue();
        return $this;
    }

    public function setExample(ComponentsExample $example): self
    {
        $exception = $this->validateValue($example->toExample()->getLiteralValue());
        if ($exception) {
            throw $exception;
        }

        $this->example = $example;
        return $this;
    }

    public function isValueValid($value): array
    {
        if (is_bool($value)) {
            return [];
        }
        if ($this->isNullable->toBool() && is_null($value)) {
            return [];
        }

        $errors = [];

        $errorMessage = $this->getWrongTypeMessage('bool', $value);
        $errors[] = $this->name ?
            FieldMessage::generate([$this->name->toString()], $errorMessage) :
            $errorMessage;
        return $errors;
    }

    public function toOpenApiSpecification(): array
    {
        $specification = [
            'type' => $this->type->getType()
        ];
        if ($this->description) {
            $specification['description'] = $this->description->toString();
        }
        if ($this->isDeprecated->toBool()) {
            $specification['deprecated'] = true;
        }
        if ($this->isNullable()) {
            $specification['nullable'] = true;
        }
        if ($this->example) {
            $specification['example'] = $this->example->getLiteralValue();
        }
        return $specification;
    }

    public function getValueFromTrimmedCastedString(string $value): bool
    {
        if (strcasecmp($value, 'false') === 0) {
            return false;
        }

        return (bool)$value;
    }
}