<?php declare(strict_types=1);

namespace App\OpenApiSpecification\ApiComponents\ComponentsSchema;

use App\Message\FieldMessage;
use App\OpenApiSpecification\ApiComponents\ComponentsExample;
use App\OpenApiSpecification\ApiComponents\ComponentsSchema\Schema\SchemaAdditionalProperty;
use App\OpenApiSpecification\ApiComponents\ComponentsSchema\Schema\SchemaIsDeprecated;
use App\OpenApiSpecification\ApiComponents\ComponentsSchema\Schema\SchemaIsNullable;
use App\OpenApiSpecification\ApiComponents\ComponentsSchema\Schema\SchemaIsRequired;
use App\OpenApiSpecification\ApiComponents\ComponentsSchema\Schema\SchemaName;
use App\OpenApiSpecification\ApiComponents\ComponentsSchema\Schema\SchemaType;

final class MapSchema extends Schema
{
    private SchemaAdditionalProperty $additionalProperty;

    private function __construct(
        SchemaAdditionalProperty $additionalProperty,
        SchemaIsRequired $isRequired,
        ?SchemaName $name = null,
        ?SchemaIsNullable $isNullable = null,
        ?ComponentsExample $example = null,
        ?SchemaIsDeprecated $isDeprecated = null
    ) {
        $this->additionalProperty = $additionalProperty;
        $this->isDeprecated = $isDeprecated ?? SchemaIsDeprecated::generateFalse();
        $this->isRequired = $isRequired;
        $this->name = $name;
        $this->isNullable = $isNullable ?? SchemaIsNullable::generateFalse();
        $this->example = $example;
    }

    public function getType(): ?SchemaType
    {
        return null;
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
        if ($this->isNullable->toBool() && is_null($values)) {
            return [];
        }
        if (!is_array($values)) {
            $errorMessage = $this->getWrongTypeMessage('map (array)', $values);
            return $this->name ?
                [FieldMessage::generate([$this->name->toString()], $errorMessage)] :
                [$errorMessage];
        }

        foreach (array_values($values) as $value) {
            $subErrors = $this->additionalProperty->getSchema()->isValueValid($value);
            if ($subErrors) {
                if (!$this->name) {
                    $errors = array_merge($errors, $subErrors);
                }

                foreach ($subErrors as $error) {
                    if ($error instanceof FieldMessage) {
                        $errors[] = FieldMessage::generate(
                            $error->getPath()->prepend($this->name->toString())->toArray(),
                            $error->getMessage()
                        );
                        continue;
                    }

                    $errors[] = FieldMessage::generate(
                        [$this->name->toString()],
                        $error
                    );
                }
            }
        }

        return $errors;
    }

    public function getAdditionalPropertySchema(): Schema
    {
        return $this->additionalProperty->getSchema()->toSchema();
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

    public function setName(string $name): self
    {
        $this->name = SchemaName::fromString($name);
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

    public function toOpenApiSpecification(): array
    {
        $specification =  [
            'type' => 'object',
            'additionalProperties' => $this->additionalProperty->toOpenApiSpecification()
        ];
        if ($this->isNullable()) {
            $specification['nullable'] = true;
        }
        if ($this->isDeprecated->toBool()) {
            $specification['deprecated'] = true;
        }
        if ($this->example) {
            $specification['example'] = $this->example->getLiteralValue();
        }
        return $specification;
    }

    protected function getValueFromTrimmedCastedString(string $value): array
    {
        $object = [];
        $json = json_decode($value, true);
        foreach ($json as $key => $entry) {
            $object[$key] = $this->additionalProperty->getSchema()->getValueFromCastedString(json_encode($entry));
        }

        return $object;
    }
}