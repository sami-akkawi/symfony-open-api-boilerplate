<?php declare(strict_types=1);

namespace App\ApiV1Bundle\Schema;

use App\OpenApiSpecification\ApiComponents\ComponentsSchema\ArraySchema;
use App\OpenApiSpecification\ApiComponents\ComponentsSchema\DiscriminatorSchema;
use App\OpenApiSpecification\ApiComponents\ComponentsSchema\IntegerSchema;
use App\OpenApiSpecification\ApiComponents\ComponentsSchema\ObjectSchema;
use App\OpenApiSpecification\ApiComponents\ComponentsSchema\StringSchema;
use App\OpenApiSpecification\ApiComponents\ComponentsSchema\Schema;
use App\OpenApiSpecification\ApiComponents\ComponentsSchemas;

final class FieldMessageSchema extends AbstractSchema
{
    private ObjectSchema $schema;

    private function __construct(ObjectSchema $schema)
    {
        $this->schema = $schema;
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
        return ObjectSchema::generate(
            ComponentsSchemas::generate()->addSchema(
                ArraySchema::generate(
                    DiscriminatorSchema::generateAnyOf()
                    ->addSchema(StringSchema::generate())
                    ->addSchema(IntegerSchema::generate())
                )->setName('path')
                    ->setDescription('The path to the field with errors.')
                    ->setMinimumItems(1)
            )->addSchema(MessageSchema::getReferenceSchema()->setName('message'))
        );
    }
}