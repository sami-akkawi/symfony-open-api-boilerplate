<?php declare(strict_types=1);

namespace App\ApiV1Bundle\Schema;

use App\OpenApiSpecification\ApiComponents\Schema\ArraySchema;
use App\OpenApiSpecification\ApiComponents\Schema\Schema;
use App\OpenApiSpecification\ApiComponents\Schema\ObjectSchema;
use App\OpenApiSpecification\ApiComponents\Schema\StringSchema;
use App\OpenApiSpecification\ApiComponents\ComponentsSchemas;

final class ErrorResponseSchema extends AbstractSchema
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
            ComponentsSchemas::generate()
                ->addSchema(
                    StringSchema::generate()
                        ->setName('errorType')
                        ->require())
                ->addSchema(
                    ArraySchema::generate(MessageSchema::getReferenceSchema())
                        ->setName('messages')
                        ->makeValuesUnique())
                ->addSchema(
                    ArraySchema::generate(FieldMessageSchema::getReferenceSchema())
                        ->setName('fieldMessages')
                        ->makeValuesUnique())
        );
    }
}