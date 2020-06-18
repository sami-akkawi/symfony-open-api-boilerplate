<?php declare(strict_types=1);

namespace App\OpenApiSpecification\ApiComponents\Schema;

use App\OpenApiSpecification\ApiComponents\Example;
use App\OpenApiSpecification\ApiComponents\Schema\Schema\DiscriminatorSchemaType;
use App\OpenApiSpecification\ApiComponents\Schema\Schema\SchemaDescription;
use App\OpenApiSpecification\ApiComponents\Schema\Schema\SchemaIsDeprecated;
use App\OpenApiSpecification\ApiComponents\Schema\Schema\SchemaIsNullable;
use App\OpenApiSpecification\ApiComponents\Schema\Schema\SchemaIsRequired;
use App\OpenApiSpecification\ApiComponents\Schema\Schema\SchemaName;
use App\OpenApiSpecification\ApiComponents\Schema;
use App\OpenApiSpecification\ApiComponents\Schemas;
use App\OpenApiSpecification\ApiException\SpecificationException;
use LogicException;
use Symfony\Component\Uid\Uuid;

final class DiscriminatorSchema extends DetailedSchema
{
    protected DiscriminatorSchemaType $type;
    protected Schemas $schemas;
    protected ?SchemaName $name;
    protected ?SchemaDescription $description;

    private function __construct(
        DiscriminatorSchemaType $type,
        SchemaIsRequired $isRequired,
        Schemas $schemas,
        ?SchemaName $name = null,
        ?SchemaDescription $description = null,
        ?SchemaIsNullable $isNullable = null,
        ?Example $example = null,
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
        return new self(DiscriminatorSchemaType::generateAnyOf(), SchemaIsRequired::generateFalse(), Schemas::generate());
    }

    public static function generateAllOf(): self
    {
        return new self(DiscriminatorSchemaType::generateAllOf(), SchemaIsRequired::generateFalse(), Schemas::generate());
    }

    public static function generateOneOf(): self
    {
        return new self(DiscriminatorSchemaType::generateOneOf(), SchemaIsRequired::generateFalse(), Schemas::generate());
    }

    public function addSchema(Schema $schema): self
    {
        if ($this->type->isAllOf() && $schema instanceof PrimitiveSchema) {
            $name = $schema->getName() ?? SchemaName::fromString(Uuid::v4()->toRfc4122());
            $schema = ObjectSchema::generate(Schemas::generate()->addSchema($schema->setName($name->toString())));
        }

        return new self(
            $this->type,
            $this->isRequired,
            $this->schemas->addSchema($schema->setName(Uuid::v4()->toRfc4122())),
            $this->name,
            $this->description,
            $this->isNullable,
            $this->example,
            $this->isDeprecated
        );
    }

    public function setDescription(string $description): self
    {
        return new self(
            $this->type,
            $this->isRequired,
            $this->schemas,
            $this->name,
            SchemaDescription::fromString($description),
            $this->isNullable,
            $this->example,
            $this->isDeprecated
        );
    }

    public function makeNullable(): self
    {
        return new self(
            $this->type,
            $this->isRequired,
            $this->schemas,
            $this->name,
            $this->description,
            SchemaIsNullable::generateTrue(),
            $this->example,
            $this->isDeprecated
        );
    }

    public function require(): self
    {
        return new self(
            $this->type,
            SchemaIsRequired::generateTrue(),
            $this->schemas,
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
            $this->type,
            SchemaIsRequired::generateFalse(),
            $this->schemas,
            $this->name,
            $this->description,
            $this->isNullable,
            $this->example,
            $this->isDeprecated
        );
    }

    public function requireOnly(array $fieldNames): self
    {
        if ($this->type->isOneOf() || $this->type->isAnyOf()) {
            throw SpecificationException::generateRequireOnlyWorksOnlyOnAllOf();
        }

        $schemas = Schemas::generate();
        /** @var ObjectSchema $schema */
        foreach ($this->schemas->toArrayOfSchemas() as $schema) {
            $schema = $schema->toDetailedSchema();
            $newSchema = $schema->requireOnly($fieldNames)->setName(Uuid::v4()->toRfc4122());
            $schemas = $schemas->addSchema($newSchema);
        }

        return new self(
            $this->type,
            $this->isRequired,
            $schemas,
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
            $this->type,
            $this->isRequired,
            $this->schemas,
            $this->name,
            $this->description,
            $this->isNullable,
            $this->example,
            SchemaIsDeprecated::generateTrue()
        );
    }

    public function setName(string $name): self
    {
        return new self(
            $this->type,
            $this->isRequired,
            $this->schemas,
            SchemaName::fromString($name),
            $this->description,
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
            $this->type,
            $this->isRequired,
            $this->schemas,
            $this->name,
            $this->description,
            $this->isNullable,
            $example,
            $this->isDeprecated
        );
    }

    public function isValueValid($value): array
    {
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
            $schema = $this->schemas->getSchema($name);
            $errors[$name] = $schema->isValueValid($value);
        }

        $numberOfValid = 0;
        foreach ($errors as $error) {
            if (empty($error)) {
                $numberOfValid++;
            }
        }

        return $numberOfValid === 1 ? [] : ["Exactly ONE value should match, $numberOfValid matched"];
    }

    private function isValueValidForAnyOf($value): array
    {
        $errors = [];
        foreach ($this->schemas->getSchemaNames() as $name) {
            $schema = $this->schemas->getSchema($name);
            $errors[$name] = $schema->isValueValid($value);
        }

        foreach ($errors as $error) {
            if (empty($error)) {
                return [];
            }
        }

        return $errors;
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
        if ($this->example) {
            $specification['example'] = $this->example->getLiteralValue();
        }
        $specification[$this->type->toString()] = array_values($this->schemas->toOpenApiSpecification());

        return $specification;
    }
}