<?php declare(strict=1);

namespace App\ApiV1Bundle\ApiSpecification\ApiComponents\Schema;

use App\ApiV1Bundle\ApiSpecification\ApiComponents\Example;
use App\ApiV1Bundle\ApiSpecification\ApiComponents\Schema\Schema\SchemaAdditionalProperty;
use App\ApiV1Bundle\ApiSpecification\ApiComponents\Schema\Schema\SchemaIsNullable;
use App\ApiV1Bundle\ApiSpecification\ApiComponents\Schema\Schema\SchemaIsRequired;
use App\ApiV1Bundle\ApiSpecification\ApiComponents\Schema\Schema\SchemaName;

final class MapSchema extends DetailedSchema
{
    private SchemaAdditionalProperty $additionalProperty;

    private function __construct(
        SchemaAdditionalProperty $additionalProperty,
        SchemaIsRequired $isRequired,
        ?SchemaName $name = null,
        ?SchemaIsNullable $isNullable = null,
        ?Example $example = null
    ) {
        $this->additionalProperty = $additionalProperty;
        $this->isRequired = $isRequired;
        $this->name = $name;
        $this->isNullable = $isNullable ?? SchemaIsNullable::generateFalse();
        $this->example = $example;
    }

    public static function generateStringMap(): self
    {
        return new self(SchemaAdditionalProperty::fromStringSchema(StringSchema::generate()), SchemaIsRequired::generateFalse());
    }

    public static function fromReferenceSchema(ReferenceSchema $schema): self
    {
        return new self(SchemaAdditionalProperty::fromReferenceSchema($schema), SchemaIsRequired::generateFalse());
    }

    public function isValueValid($values): array
    {
        $errors = [];
        if (!is_array($values)) {
            $errors[] = $this->getWrongTypeMessage('array', $values);
            return $errors;
        }
        foreach (array_values($values) as $value) {
            $subError = $this->additionalProperty->getSchema()->isValueValid($value);
            if ($subError) {
                $errors[] = $subError;
            }
        }

        return $errors;
    }

    public function makeNullable(): self
    {
        return new self(
            $this->additionalProperty,
            $this->isRequired,
            $this->name,
            SchemaIsNullable::generateTrue(),
            $this->example
        );
    }

    public function require(): self
    {
        return new self(
            $this->additionalProperty,
            SchemaIsRequired::generateTrue(),
            $this->name,
            $this->isNullable,
            $this->example
        );
    }

    public function setName(string $name): self
    {
        return new self(
            $this->additionalProperty,
            $this->isRequired,
            SchemaName::fromString($name),
            $this->isNullable,
            $this->example
        );
    }

    public function setExample(Example $example): self
    {
        $exception = $this->validateValue($example->toDetailedExample()->toMixed());
        if ($exception) {
            throw $exception;
        }

        return new self(
            $this->additionalProperty,
            $this->isRequired,
            $this->name,
            $this->isNullable,
            $example
        );
    }

    public function toOpenApiSpecification(): array
    {
        $specification =  [
            'type' => 'object',
            'additionalProperties' => $this->additionalProperty->toOpenApiSpecification()
        ];
        if ($this->isNullable()) {
            $specification['nullable'] = true;
        }
        if ($this->example) {
            $specification['example'] = $this->example->toMixed();
        }
        return $specification;
    }
}