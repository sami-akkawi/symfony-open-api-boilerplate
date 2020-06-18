<?php declare(strict=1);

namespace App\ApiV1Bundle\Schema;

use App\OpenApiSpecification\ApiComponents\Example;
use App\OpenApiSpecification\ApiComponents\Example\DetailedExample;
use App\OpenApiSpecification\ApiComponents\Schema\DetailedSchema;
use App\OpenApiSpecification\ApiComponents\Schema\MapSchema;
use App\OpenApiSpecification\ApiComponents\Schema\ObjectSchema;
use App\OpenApiSpecification\ApiComponents\Schema\StringSchema;
use App\OpenApiSpecification\ApiComponents\Schemas;

final class Translation extends AbstractSchema
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
            Schemas::generate()
                ->addSchema(StringSchema::generate()->setName('translationId'))
                ->addSchema(StringSchema::generate()->setName('defaultText'))
                ->addSchema(MapSchema::generateStringMap()
                    ->setName('placeholders')
                    ->makeNullable()
                    ->setExample(DetailedExample::generate(['%placeholder%' => 'theOfficialTranslation'])))
        );
    }
}