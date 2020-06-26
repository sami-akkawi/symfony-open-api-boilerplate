<?php declare(strict_types=1);

namespace App\OpenApiSpecification\ApiComponents\Schema\Schema;

use App\OpenApiSpecification\ApiComponents\Schema\ReferenceSchema;
use App\OpenApiSpecification\ApiComponents\Schema\StringSchema;
use App\OpenApiSpecification\ApiComponents\ComponentsSchema;

final class SchemaAdditionalProperty
{
    /** @var StringSchema|ReferenceSchema */
    private $schema;

    private function __construct($schema)
    {
        $this->schema = $schema;
    }

    public static function fromStringSchema(StringSchema $schema): self
    {
        return new self($schema);
    }

    public static function fromReferenceSchema(ReferenceSchema $schema): self
    {
        return new self($schema);
    }

    public function isStringSchema(): bool
    {
        return get_class($this->schema) === StringSchema::class;
    }

    public function isReferenceSchema(): bool
    {
        return get_class($this->schema) === ReferenceSchema::class;
    }

    public function getSchema(): ComponentsSchema
    {
        return $this->schema;
    }

    public function toOpenApiSpecification(): array
    {
        if ($this->isStringSchema()) {
            return ['type' => 'string'];
        }

        return $this->schema->toOpenApiSpecification();
    }
}