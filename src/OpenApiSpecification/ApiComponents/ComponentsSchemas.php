<?php declare(strict_types=1);

namespace App\OpenApiSpecification\ApiComponents;

use App\OpenApiSpecification\ApiComponents\ComponentsSchema\Schema\SchemaName;
use App\OpenApiSpecification\ApiException\SpecificationException;

final class ComponentsSchemas
{
    /** @var ComponentsSchema[] */
    private array $schemas;

    private function __construct(array $schemas)
    {
        $this->schemas = $schemas;
    }

    public static function generate(): self
    {
        return new self([]);
    }

    public function getRequiredSchemaNames(): array
    {
        $requiredSchemas = [];
        foreach ($this->schemas as $schema) {
            if ($schema->isRequired()) {
                if (!$schema->hasName()) {
                    throw SpecificationException::generateSchemaInSchemasNeedsAName();
                }
                $requiredSchemas[] = $schema->getName()->toString();
            }
        }
        return $requiredSchemas;
    }

    public function toArrayOfSchemas(): array
    {
        return $this->schemas;
    }

    public function unRequire(array $fieldNames): self
    {
        $schemas = [];
        foreach ($this->schemas as $schema) {
            if (in_array($schema->getName()->toString(), $fieldNames)) {
                $schema = $schema->unRequire();
            }
            $schemas[] = $schema;
        }
        return new self($schemas);
    }

    public function hasSchema(SchemaName $name): bool
    {
        foreach ($this->schemas as $schema) {
            if ($schema->getName()->isIdenticalTo($name)) {
                return true;
            }
        }

        return false;
    }

    public function addSchema(ComponentsSchema $schema): self
    {
        if (!$schema->hasName()) {
            throw SpecificationException::generateSchemaInSchemasNeedsAName();
        }
        if ($this->hasSchema($schema->getName())) {
            throw SpecificationException::generateDuplicateDefinitionException($schema->getName()->toString());
        }
        return new self(array_merge($this->schemas, [$schema]));
    }

    public function getSchemaNames(): array
    {
        $requiredSchemas = [];
        foreach ($this->schemas as $schema) {
            $requiredSchemas[] = $schema->getName()->toString();
        }
        return $requiredSchemas;
    }

    public function getSchema(string $name): ?ComponentsSchema
    {
        foreach ($this->schemas as $schema) {
            if ($schema->getName()->toString() === $name) {
                return $schema;
            }
        }
        return null;
    }

    public function toOpenApiSpecification(): array
    {
        $schemas = [];
        foreach ($this->schemas as $schema) {
            $schemas[$schema->getName()->toString()] = $schema->toOpenApiSpecification();
        }
        return $schemas;
    }

    public function toOpenApiSpecificationForComponents(): array
    {
        $schemas = $this->toOpenApiSpecification();
        ksort($schemas);
        return $schemas;
    }

    public function isDefined(): bool
    {
        return (bool)count($this->schemas);
    }
}