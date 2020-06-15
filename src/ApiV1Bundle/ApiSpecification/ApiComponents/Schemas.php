<?php declare(strict=1);

namespace App\ApiV1Bundle\ApiSpecification\ApiComponents;

use App\ApiV1Bundle\ApiSpecification\ApiComponents\Schema\Schema\SchemaName;
use App\ApiV1Bundle\ApiSpecification\ApiException\SpecificationException;

final class Schemas
{
    /** @var Schema[] */
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

    public function hasSchema(SchemaName $name): bool
    {
        foreach ($this->schemas as $schema) {
            if ($schema->getName()->isIdenticalTo($name)) {
                return true;
            }
        }

        return false;
    }

    public function addSchema(Schema $schema): self
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

    public function getSchema(string $name): ?Schema
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