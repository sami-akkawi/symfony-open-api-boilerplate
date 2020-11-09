<?php declare(strict_types=1);

namespace App\ApiV1Bundle\Schema;

use App\Message\Message;
use App\Message\MessageType;
use App\OpenApiSpecification\ApiComponents\ComponentsExample\Example;
use App\OpenApiSpecification\ApiComponents\ComponentsSchema\Schema;
use App\OpenApiSpecification\ApiComponents\ComponentsSchema\DiscriminatorSchema;
use App\OpenApiSpecification\ApiComponents\ComponentsSchema\MapSchema;
use App\OpenApiSpecification\ApiComponents\ComponentsSchema\ObjectSchema;
use App\OpenApiSpecification\ApiComponents\ComponentsSchema\Schema\SchemaType;
use App\OpenApiSpecification\ApiComponents\ComponentsSchema\StringSchema;
use App\OpenApiSpecification\ApiComponents\ComponentsSchemas;

final class UserSchema extends AbstractSchema
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
        return ObjectSchema::generateDataSchema(
            ComponentsSchemas::generate()
                ->addSchema(StringSchema::generate()->setName('id')->setFormat(SchemaType::STRING_UUID_FORMAT))
                ->addSchema(StringSchema::generate()->setName('email')->setFormat(SchemaType::STRING_EMAIL_FORMAT))
                ->addSchema(StringSchema::generate()->setName('username'))
        );
    }
}