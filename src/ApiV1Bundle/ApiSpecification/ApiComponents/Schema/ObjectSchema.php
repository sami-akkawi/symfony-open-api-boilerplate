<?php declare(strict=1);

namespace App\ApiV1Bundle\ApiSpecification\ApiComponents\Schema;

use App\ApiV1Bundle\ApiSpecification\ApiComponents\Example;
use App\ApiV1Bundle\ApiSpecification\ApiComponents\Schema\Schema\SchemaDescription;
use App\ApiV1Bundle\ApiSpecification\ApiComponents\Schema\Schema\SchemaIsDeprecated;
use App\ApiV1Bundle\ApiSpecification\ApiComponents\Schema\Schema\SchemaIsNullable;
use App\ApiV1Bundle\ApiSpecification\ApiComponents\Schema\Schema\SchemaIsRequired;
use App\ApiV1Bundle\ApiSpecification\ApiComponents\Schema\Schema\SchemaName;
use App\ApiV1Bundle\ApiSpecification\ApiComponents\Schema\Schema\SchemaType;
use App\ApiV1Bundle\ApiSpecification\ApiComponents\Schemas;
use App\ApiV1Bundle\ApiSpecification\ApiException\SpecificationException;

final class ObjectSchema extends DetailedSchema
{
    protected ?SchemaName $name;
    private SchemaType $type;
    private Schemas $properties;
    private ?SchemaDescription $description;

    private function __construct(
        Schemas $properties,
        SchemaIsRequired $isRequired,
        ?SchemaName $name = null,
        ?SchemaDescription $description = null,
        ?SchemaIsNullable $isNullable = null,
        ?Example $example = null,
        ?SchemaIsDeprecated $isDeprecated = null
    ) {
        if (!$properties->isDefined()) {
            throw SpecificationException::generateObjectSchemaNeedsProperties($name ? $name->toString() : 'no_name');
        }
        $this->name = $name;
        $this->isRequired = $isRequired;
        $this->isDeprecated = $isDeprecated ?? SchemaIsDeprecated::generateFalse();
        $this->type = SchemaType::generateObject();
        $this->properties = $properties;
        $this->description = $description;
        $this->isNullable = $isNullable ?? SchemaIsNullable::generateFalse();
        $this->example = $example;
    }

    public function setName(string $name): self
    {
        return new self(
            $this->properties,
            $this->isRequired,
            SchemaName::fromString($name),
            $this->description,
            $this->isNullable,
            $this->example,
            $this->isDeprecated
        );
    }

    public function hasProperty(string $propertyName): bool
    {
        return $this->properties->hasSchema(SchemaName::fromString($propertyName));
    }

    public function require(): self
    {
        return new self(
            $this->properties,
            SchemaIsRequired::generateTrue(),
            $this->name,
            $this->description,
            $this->isNullable,
            $this->example,
            $this->isDeprecated
        );
    }

    public function unRequire(): self
    {
        return new self(
            $this->properties,
            SchemaIsRequired::generateFalse(),
            $this->name,
            $this->description,
            $this->isNullable,
            $this->example,
            $this->isDeprecated
        );
    }

    public function requireOnly(array $fieldNames): self
    {
        $requiredFields = $this->getRequiredProperties();
        $fieldsToUnRequire = [];
        foreach ($requiredFields as $requiredField) {
            if (!in_array($requiredField, $fieldNames)) {
                $fieldsToUnRequire[] = $requiredField;
            }
        }

        return new self(
            $this->properties->unRequire($fieldsToUnRequire),
            $this->isRequired,
            $this->name,
            $this->description,
            $this->isNullable,
            $this->example,
            $this->isDeprecated
        );
    }

    public function deprecate(): self
    {
        return new self(
            $this->properties,
            $this->isRequired,
            $this->name,
            $this->description,
            $this->isNullable,
            $this->example,
            SchemaIsDeprecated::generateTrue()
        );
    }

    public static function generate(Schemas $properties): self
    {
        return new self($properties, SchemaIsRequired::generateFalse());
    }

    public static function generateDataSchema(Schemas $properties): self
    {
        return new self($properties, SchemaIsRequired::generateTrue(), SchemaName::fromString('data'));
    }

    public function setDescription(string $description): self
    {
        return new self(
            $this->properties,
            $this->isRequired,
            $this->name,
            SchemaDescription::fromString($description),
            $this->isNullable,
            $this->example,
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
            $this->properties,
            $this->isRequired,
            $this->name,
            $this->description,
            $this->isNullable,
            $example,
            $this->isDeprecated
        );
    }

    public function isValueValid($object): array
    {
        $errors = [];
        foreach (array_keys($object) as $key) {
            if (!$this->properties->hasSchema(SchemaName::fromString($key))) {
                $errors[$key] = 'Key defined in object but not in schema!';
            }
            $schema = $this->properties->getSchema($key);
            $subErrors = $schema->isValueValid($object[$key]);
            if ($subErrors) {
                $errors[] = $subErrors;
            }
        }

        $requiredSchemaNames = $this->properties->getRequiredSchemaNames();
        foreach ($requiredSchemaNames as $name) {
            if (!in_array(array_keys($object), $name)) {
                $errors[$name] = 'Property is required, but never defined!';
            }
            $schema = $this->properties->getSchema($name);
            $subErrors = $schema->isValueValid($object[$name]);
            if ($subErrors) {
                $errors[] = $subErrors;
            }
        }

        return $errors;
    }

    public function makeNullable(): self
    {
        return new self(
            $this->properties,
            $this->isRequired,
            $this->name,
            $this->description,
            SchemaIsNullable::generateTrue(),
            $this->example,
            $this->isDeprecated
        );
    }

    private function getRequiredProperties(): array
    {
        return $this->properties->getRequiredSchemaNames();
    }

    public function toOpenApiSpecification(): array
    {
        $specification = [
            'type' => $this->type->getType(),
            'properties' => $this->properties->toOpenApiSpecification()
        ];
        $requiredProperties = $this->getRequiredProperties();
        if (!empty($requiredProperties)) {
            $specification['required'] = $requiredProperties;
        }
        if ($this->description) {
            $specification['description'] = $this->description->toString();
        }
        if ($this->isNullable()) {
            $specification['nullable'] = true;
        }
        if ($this->example) {
            $specification['example'] = $this->example->getLiteralValue();
        }
        return $specification;
    }
}