<?php declare(strict_types=1);

namespace App\ApiV1Bundle\Schema;

use App\OpenApiSpecification\ApiComponents\Schema\ArraySchema;
use App\OpenApiSpecification\ApiComponents\Schema\DiscriminatorSchema;
use App\OpenApiSpecification\ApiComponents\Schema\IntegerSchema;
use App\OpenApiSpecification\ApiComponents\Schema\ObjectSchema;
use App\OpenApiSpecification\ApiComponents\Schema\StringSchema;
use App\OpenApiSpecification\ApiComponents\Schema\DetailedSchema;
use App\OpenApiSpecification\ApiComponents\Schemas;

final class FieldMessage extends AbstractSchema
{
    private ObjectSchema $schema;

    private function __construct(ObjectSchema $schema)
    {
        $this->schema = $schema;
    }

    public function toDetailedSchema(): DetailedSchema
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

    protected static function getOpenApiSchemaWithoutName(): DetailedSchema
    {
        return ObjectSchema::generate(
            Schemas::generate()->addSchema(
                ArraySchema::generate(
                    DiscriminatorSchema::generateAnyOf()
                    ->addSchema(StringSchema::generate())
                    ->addSchema(IntegerSchema::generate())
                )->setName('path')
                    ->setDescription('The path to the field with errors.')
                    ->setMinimumItems(1)
            )->addSchema(Message::getReferenceSchema()->setName('message'))
        );
    }
}