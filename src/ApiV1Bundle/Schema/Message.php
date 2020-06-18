<?php declare(strict=1);

namespace App\ApiV1Bundle\Schema;

use App\OpenApiSpecification\ApiComponents\Schema\DetailedSchema;
use App\OpenApiSpecification\ApiComponents\Schema\DiscriminatorSchema;
use App\OpenApiSpecification\ApiComponents\Schema\MapSchema;
use App\OpenApiSpecification\ApiComponents\Schema\ObjectSchema;
use App\OpenApiSpecification\ApiComponents\Schema\Schema\SchemaType;
use App\OpenApiSpecification\ApiComponents\Schema\StringSchema;
use App\OpenApiSpecification\ApiComponents\Schemas;

final class Message extends AbstractSchema
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
        return DiscriminatorSchema::generateAllOf()
            ->addSchema(StringSchema::generate()
                ->setName('id')
                ->setFormat(SchemaType::STRING_UUID_FORMAT))
            ->addSchema(StringSchema::generate()
                ->setName('type')
                ->setOptions(['info', 'success', 'warning', 'error']))
            ->addSchema(Translation::getReferenceSchema());
    }
}