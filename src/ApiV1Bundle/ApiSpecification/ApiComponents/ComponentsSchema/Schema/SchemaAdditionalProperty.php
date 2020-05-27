<?php declare(strict=1);

namespace App\ApiV1Bundle\ApiSpecification\ApiComponents\ComponentsSchema\Schema;

use App\ApiV1Bundle\ApiSpecification\ApiComponents\ComponentsSchema\ReferenceSchema;
use App\ApiV1Bundle\ApiSpecification\ApiComponents\ComponentsSchema\StringSchema;
use App\ApiV1Bundle\ApiSpecification\ApiComponents\Schema;

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

    public function getSchema(): Schema
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