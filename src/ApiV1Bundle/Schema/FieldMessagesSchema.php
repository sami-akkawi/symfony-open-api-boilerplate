<?php declare(strict_types=1);

namespace App\ApiV1Bundle\Schema;

use App\OpenApiSpecification\ApiComponents\ComponentsSchema\ArraySchema;
use App\OpenApiSpecification\ApiComponents\ComponentsSchema\DiscriminatorSchema;
use App\OpenApiSpecification\ApiComponents\ComponentsSchema\IntegerSchema;
use App\OpenApiSpecification\ApiComponents\ComponentsSchema\ObjectSchema;
use App\OpenApiSpecification\ApiComponents\ComponentsSchema\StringSchema;
use App\OpenApiSpecification\ApiComponents\ComponentsSchema\Schema;
use App\OpenApiSpecification\ApiComponents\ComponentsSchemas;

final class FieldMessagesSchema extends AbstractSchema
{
    private Schema $schema;

    private function __construct(ArraySchema $detailedSchema)
    {
        $this->schema = $detailedSchema;
    }

    public static function generate(): self
    {
        /** @var ArraySchema $schema */
        $schema = self::getOpenApiSchemaWithoutName();
        return new self($schema);
    }

    public function toDetailedSchema(): Schema
    {
        return $this->schema;
    }

    public static function getAlwaysRequiredFields(): array
    {
        return [];
    }

    public function requireOnly(array $fieldNames): self
    {
        $requireAlways = self::getAlwaysRequiredFields();
        $newSchema = $this->schema->requireOnly(array_merge($requireAlways , $fieldNames));
        return new self($newSchema);
    }

    protected static function getOpenApiSchemaWithoutName(): Schema
    {
        return ArraySchema::generate(FieldMessageSchema::getReferenceSchema())->makeValuesUnique();
    }
}