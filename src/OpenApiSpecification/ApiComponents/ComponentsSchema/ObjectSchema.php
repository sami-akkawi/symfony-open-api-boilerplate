<?php declare(strict_types=1);

namespace App\OpenApiSpecification\ApiComponents\ComponentsSchema;

use App\Message\FieldMessage;
use App\Message\Message;
use App\OpenApiSpecification\ApiComponents\ComponentsExample;
use App\OpenApiSpecification\ApiComponents\ComponentsSchema\Schema\SchemaDescription;
use App\OpenApiSpecification\ApiComponents\ComponentsSchema\Schema\SchemaIsDeprecated;
use App\OpenApiSpecification\ApiComponents\ComponentsSchema\Schema\SchemaIsNullable;
use App\OpenApiSpecification\ApiComponents\ComponentsSchema\Schema\SchemaIsRequired;
use App\OpenApiSpecification\ApiComponents\ComponentsSchema\Schema\SchemaName;
use App\OpenApiSpecification\ApiComponents\ComponentsSchema\Schema\SchemaType;
use App\OpenApiSpecification\ApiComponents\ComponentsSchemas;
use App\OpenApiSpecification\ApiException\SpecificationException;

final class ObjectSchema extends Schema
{
    protected ?SchemaName $name;
    private SchemaType $type;
    private ComponentsSchemas $properties;
    private ?SchemaDescription $description;

    private function __construct(
        ComponentsSchemas $properties,
        SchemaIsRequired $isRequired,
        ?SchemaName $name = null,
        ?SchemaDescription $description = null,
        ?SchemaIsNullable $isNullable = null,
        ?ComponentsExample $example = null,
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

    public function getType(): SchemaType
    {
        return $this->type;
    }

    public function setName(string $name): self
    {
        $this->name = SchemaName::fromString($name);
        return $this;
    }

    public static function generateEmpty(): self
    {
        return new self(ComponentsSchemas::generate(), SchemaIsRequired::generateFalse());
    }

    public function isEmpty(): bool
    {
        return !$this->properties->isDefined();
    }

    public function hasProperty(string $propertyName): bool
    {
        return $this->properties->hasSchema(SchemaName::fromString($propertyName));
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

    public function requireOnly(array $fieldNames): self
    {
        $requiredFields = $this->getRequiredProperties();
        $fieldsToUnRequire = [];
        foreach ($requiredFields as $requiredField) {
            if (!in_array($requiredField, $fieldNames)) {
                $fieldsToUnRequire[] = $requiredField;
            }
        }

        $this->properties = $this->properties->unRequire($fieldsToUnRequire);
        return $this;
    }

    public function getProperties(): ComponentsSchemas
    {
        return $this->properties;
    }

    public static function generate(ComponentsSchemas $properties): self
    {
        return new self($properties, SchemaIsRequired::generateFalse());
    }

    public static function generateDataSchema(ComponentsSchemas $properties): self
    {
        return new self($properties, SchemaIsRequired::generateTrue(), SchemaName::fromString('data'));
    }

    public function setDescription(string $description): self
    {
        $this->description = SchemaDescription::fromString($description);
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

    public function isValueValid($object): array
    {
        if ($this->isNullable->toBool() && is_null($object)) {
            return [];
        }
        if (!is_array($object)) {
            return [FieldMessage::generate(
                $this->name ? [$this->name->toString()] : [],
                Message::generateError(
                    'not_an_object',
                    'Value is not a JSON representation of the object! ' . ($object ? print_r($object, true) : 'NULL'),
                    [
                        '%suppliedObject%' => $object ? print_r($object, true) : 'NULL'
                    ]

                )
            )];
        }

        $errors = [];
        $requiredSchemaNames = $this->properties->getRequiredSchemaNames();
        foreach ($requiredSchemaNames as $name) {
            if (!in_array($name, array_keys($object))) {
                $errors[] = FieldMessage::generate(
                    [$name],
                    Message::generateError(
                        'is_required_field',
                        "$name is a required field.",
                        [
                            '%fieldName%' => $name
                        ]
                    )
                );
                continue;
            }
            $schema = $this->properties->findSchemaByName($name);
            $subErrors = $schema->isValueValid($object[$name]);
            if ($subErrors) {
                foreach ($subErrors as $error) {
                    if ($error instanceof FieldMessage) {
                        $errors[] = $this->name ? $error->prependPath($this->name->toString()): $error;
                        continue;
                    }

                    $errors[] = $error;
                }
            }
        }

        foreach (array_keys($object) as $key) {
            if (is_int($key)) {
                $key = (string)$key;
            }
            if (!$this->properties->hasSchema(SchemaName::fromString($key))) {
                $errors[] = Message::generateError(
                    'key_not_part_of_object',
                    "$key is not defined in object",
                    [
                        '%undefinedKey' => $key
                    ]
                );
                continue;
            }
            $schema = $this->properties->findSchemaByName($key);
            if ($schema->isRequired()) {
                continue;
            }
            $subErrors = $schema->isValueValid($object[$key]);
            if ($subErrors) {
                foreach ($subErrors as $error) {
                    if ($error instanceof FieldMessage) {
                        $errors[] = $this->name ? $error->prependPath($this->name->toString()): $error;
                        continue;
                    }

                    $errors[] = $error;
                }
            }
        }

        return $errors;
    }

    public function makeNullable(): self
    {
        $this->isNullable = SchemaIsNullable::generateTrue();
        return $this;
    }

    public function deprecate(): self
    {
        $this->isDeprecated = SchemaIsDeprecated::generateTrue();
        return $this;
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
            $schema = $this->properties->findSchemaByName($key);
            $entry = is_array($entry) ? json_encode($entry) : $entry;
            $object[$key] = $schema ? $schema->getValueFromCastedString($entry) : $entry;
        }

        return $object;
    }}