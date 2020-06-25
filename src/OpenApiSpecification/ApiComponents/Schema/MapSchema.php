<?php declare(strict_types=1);

namespace App\OpenApiSpecification\ApiComponents\Schema;

use App\Message\FieldMessage;
use App\OpenApiSpecification\ApiComponents\ComponentsExample;
use App\OpenApiSpecification\ApiComponents\Schema\Schema\SchemaAdditionalProperty;
use App\OpenApiSpecification\ApiComponents\Schema\Schema\SchemaIsDeprecated;
use App\OpenApiSpecification\ApiComponents\Schema\Schema\SchemaIsNullable;
use App\OpenApiSpecification\ApiComponents\Schema\Schema\SchemaIsRequired;
use App\OpenApiSpecification\ApiComponents\Schema\Schema\SchemaName;
use App\OpenApiSpecification\ApiComponents\Schema\Schema\SchemaType;

final class MapSchema extends DetailedSchema
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

    public function makeNullable(): self
    {
        return new self(
            $this->additionalProperty,
            $this->isRequired,
            $this->name,
            SchemaIsNullable::generateTrue(),
            $this->example,
            $this->isDeprecated
        );
    }

    public function require(): self
    {
        return new self(
            $this->additionalProperty,
            SchemaIsRequired::generateTrue(),
            $this->name,
            $this->isNullable,
            $this->example,
            $this->isDeprecated
        );
    }

    public function unRequire(): self
    {
        return new self(
            $this->additionalProperty,
            SchemaIsRequired::generateFalse(),
            $this->name,
            $this->isNullable,
            $this->example,
            $this->isDeprecated
        );
    }

    public function setName(string $name): self
    {
        return new self(
            $this->additionalProperty,
            $this->isRequired,
            SchemaName::fromString($name),
            $this->isNullable,
            $this->example,
            $this->isDeprecated
        );
    }

    public function deprecate(): self
    {
        return new self(
            $this->additionalProperty,
            $this->isRequired,
            $this->name,
            $this->isNullable,
            $this->example,
            SchemaIsDeprecated::generateTrue()
        );
    }

    public function setExample(ComponentsExample $example): self
    {
        $exception = $this->validateValue($example->toExample()->getLiteralValue());
        if ($exception) {
            throw $exception;
        }

        return new self(
            $this->additionalProperty,
            $this->isRequired,
            $this->name,
            $this->isNullable,
            $example,
            $this->isDeprecated
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