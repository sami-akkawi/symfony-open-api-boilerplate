<?php declare(strict_types=1);

namespace App\OpenApiSpecification\ApiComponents\ComponentsSchema;

use App\Message\FieldMessage;
use App\Message\Message;
use App\OpenApiSpecification\ApiComponents\ComponentsExample;
use App\OpenApiSpecification\ApiComponents\ComponentsSchema\Schema\DiscriminatorSchemaType;
use App\OpenApiSpecification\ApiComponents\ComponentsSchema\Schema\SchemaDescription;
use App\OpenApiSpecification\ApiComponents\ComponentsSchema\Schema\SchemaIsDeprecated;
use App\OpenApiSpecification\ApiComponents\ComponentsSchema\Schema\SchemaIsNullable;
use App\OpenApiSpecification\ApiComponents\ComponentsSchema\Schema\SchemaIsRequired;
use App\OpenApiSpecification\ApiComponents\ComponentsSchema\Schema\SchemaName;
use App\OpenApiSpecification\ApiComponents\ComponentsSchema\Schema\SchemaType;
use App\OpenApiSpecification\ApiComponents\ComponentsSchema;
use App\OpenApiSpecification\ApiComponents\ComponentsSchemas;
use App\OpenApiSpecification\ApiException\SpecificationException;
use LogicException;
use Symfony\Component\Uid\Uuid;

final class DiscriminatorSchema extends Schema
{
    protected DiscriminatorSchemaType $type;
    protected ComponentsSchemas $schemas;
    protected ?SchemaName $name;
    protected ?SchemaDescription $description;

    private function __construct(
        DiscriminatorSchemaType $type,
        SchemaIsRequired $isRequired,
        ComponentsSchemas $schemas,
        ?SchemaName $name = null,
        ?SchemaDescription $description = null,
        ?SchemaIsNullable $isNullable = null,
        ?ComponentsExample $example = null,
        ?SchemaIsDeprecated $isDeprecated = null
    ) {
        $this->type = $type;
        $this->isRequired = $isRequired;
        $this->isDeprecated = $isDeprecated ?? SchemaIsDeprecated::generateFalse();
        $this->schemas = $schemas;
        $this->name = $name;
        $this->description = $description;
        $this->isNullable = $isNullable ?? SchemaIsNullable::generateFalse();
        $this->example = $example;
    }

    public static function generateAnyOf(): self
    {
        return new self(DiscriminatorSchemaType::generateAnyOf(), SchemaIsRequired::generateFalse(), ComponentsSchemas::generate());
    }

    public static function generateAllOf(): self
    {
        return new self(DiscriminatorSchemaType::generateAllOf(), SchemaIsRequired::generateFalse(), ComponentsSchemas::generate());
    }

    public static function generateOneOf(): self
    {
        return new self(DiscriminatorSchemaType::generateOneOf(), SchemaIsRequired::generateFalse(), ComponentsSchemas::generate());
    }

    public function getSchemas(): ComponentsSchemas
    {
        return $this->schemas;
    }

    public function addSchema(ComponentsSchema $schema): self
    {
        if ($this->type->isAllOf() && $schema instanceof PrimitiveSchema) {
            SpecificationException::generateCannotAddPrimitiveSchemaToAllOfDiscriminator();
        }
        $name = $schema->getName() ?? SchemaName::fromString(Uuid::v4()->toRfc4122());

        $this->schemas = $this->schemas->addSchema($schema->setName($name->toString()));
        return $this;
    }

    public function requireOnly(array $fieldNames): self
    {
        if ($this->type->isOneOf() || $this->type->isAnyOf()) {
            throw SpecificationException::generateRequireOnlyWorksOnlyOnAllOf();
        }

        $schemas = ComponentsSchemas::generate();
        /** @var ObjectSchema $schema */
        foreach ($this->schemas->toArrayOfSchemas() as $schema) {
            $schema = $schema->toSchema();
            $newSchema = $schema->requireOnly($fieldNames)->setName(Uuid::v4()->toRfc4122());
            $schemas = $schemas->addSchema($newSchema);
        }

        $this->schemas = $schemas;
        return $this;
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

    public function getType(): ?SchemaType
    {
        return null;
    }

    public function isValueValid($value): array
    {
        if ($this->isNullable->toBool() && is_null($value)) {
            return [];
        }

        if ($this->type->isAllOf()) {
            return $this->isValueValidForAllOf($value);
        } elseif ($this->type->isAnyOf()) {
            return $this->isValueValidForAnyOf($value);
        } elseif ($this->type->isOneOf()) {
            return $this->isValueValidForOneOf($value);
        }

        throw new LogicException("Missing Value Validation for Discriminator Object of type " . $this->type->toString());
    }

    private function isValueValidForAllOf($value): array
    {
        return ObjectSchema::generate($this->schemas)->isValueValid($value);
    }

    private function isValueValidForOneOf($value): array
    {
        $errors = [];
        foreach ($this->schemas->getSchemaNames() as $name) {
            $schema = $this->schemas->findSchemaByName($name);
            $errors[$name] = $schema->isValueValid($value);
        }

        $numberOfValid = 0;
        foreach ($errors as $error) {
            if (empty($error)) {
                $numberOfValid++;
            }
        }

        if ($numberOfValid === 0) {
            return [];
        }

        $errorMessage = Message::generateError(
            'limitation_one_of_not_met',
            "Exactly ONE value should match, $numberOfValid matched",
            [
                '%numberOfValidFields%' => $numberOfValid
            ]
        );

        if ($this->name) {
            return [FieldMessage::generate([$this->name->toString()], $errorMessage)];
        }

        return [$errorMessage];
    }

    private function isValueValidForAnyOf($value): array
    {
        return $this->getAsObjectSchema()->isValueValid($value);
    }

    public function getAsObjectSchema(): ?ObjectSchema
    {
        if ($this->type->isAnyOf() || $this->type->isOneOf()) {
            return null;
        }

        $properties = ComponentsSchemas::generate();
        foreach ($this->schemas->toArrayOfSchemas() as $schema) {
            if ($schema instanceof ReferenceSchema) {
                $schema = $schema->toSchema();
            }

            if ($schema instanceof ObjectSchema) {
                foreach ($schema->getProperties()->toArrayOfSchemas() as $subSchema) {
                    $properties = $properties->addSchema($subSchema);
                }
                continue;
            }

            if ($schema instanceof DiscriminatorSchema) {
                $properties = $properties->addSchema($schema->getAsObjectSchema());
                continue;
            }

            $properties->addSchema($schema);
        }

        return ObjectSchema::generate($properties);
    }

    public function getDiscriminatorType(): DiscriminatorSchemaType
    {
        return $this->type;
    }

    public function getFirstSchemaByName(string $name): ?Schema
    {
        foreach ($this->schemas->toArrayOfSchemas() as $schema) {
            if ($schema instanceof ObjectSchema) {
                $foundSchema = $schema->getProperties()->findSchemaByName($name);
                if ($foundSchema) {
                    return $foundSchema->toSchema();
                }
            }
            if ($schema->getName()->toString() === $name) {
                return $schema->toSchema();
            }
        }

        return null;
    }

    public function toOpenApiSpecification(): array
    {
        $specification = [];
        if (!$this->schemas->isDefined()) {
            throw SpecificationException::generateSchemasMustBeDefined();
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
            $specification['example'] = $this->example->toOpenApiSpecification();
        }
        $specification[$this->type->toString()] = array_values($this->schemas->toOpenApiSpecification());

        return $specification;
    }

    protected function getValueFromTrimmedCastedString(string $value): array
    {
        $object = [];
        $json = json_decode($value, true);
        foreach ($json as $key => $entry) {
            $foundSchema = $this->getFirstSchemaByName($key);
            $object[$key] = $foundSchema ? $foundSchema->getValueFromCastedString(json_encode($entry)) : trim($value);
        }
        return $object;
    }
}