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
        return new self(
            $this->type,
            $this->isRequired,
            SchemaName::fromString($name),
            $this->description,
            $this->example,
            $this->isNullable,
            $this->isDeprecated
        );
    }

    public static function generate(): self
    {
        return new self(SchemaType::generateBoolean(), SchemaIsRequired::generateFalse());
    }

    public function setDescription(string $description): self
    {
        return new self(
            $this->type,
            $this->isRequired,
            $this->name,
            SchemaDescription::fromString($description),
            $this->example,
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
            $this->isNullable,
            $this->isDeprecated
        );
    }

    public function setExample(ComponentsExample $example): self
    {
        $exception = $this->validateValue($example->toExample()->getLiteralValue());
        if ($exception) {
            throw $exception;
        }

        return new self(
            $this->type,
            $this->isRequired,
            $this->name,
            $this->description,
            $example,
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
            SchemaIsNullable::generateTrue(),
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
            $this->isNullable,
            SchemaIsDeprecated::generateTrue()
        );
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
            'type' => $this->type->getType(),
            'xml' => ['name' => $this->name ? $this->name->toString() : 'body']
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