<?php declare(strict=1);

namespace App\ApiV1Bundle\Schema;

use App\ApiV1Bundle\ApiSpecification\ApiComponents\Schema\DetailedSchema;
use App\ApiV1Bundle\ApiSpecification\ApiComponents\Schema\MapSchema;
use App\ApiV1Bundle\ApiSpecification\ApiComponents\Schema\ObjectSchema;
use App\ApiV1Bundle\ApiSpecification\ApiComponents\Schema\Schema\SchemaType;
use App\ApiV1Bundle\ApiSpecification\ApiComponents\Schema\StringSchema;
use App\ApiV1Bundle\ApiSpecification\ApiComponents\Schemas;

final class Message extends AbstractSchema
{
    protected static function getOpenApiSchemaWithoutName(): DetailedSchema
    {
        return ObjectSchema::generate(
            Schemas::generate()
                ->addSchema(StringSchema::generate()
                    ->setName('id')
                    ->setFormat(SchemaType::STRING_UUID_FORMAT))
                ->addSchema(StringSchema::generate()
                    ->setName('type')
                    ->setOptions(['info', 'success', 'warning', 'error']))
                ->addSchema(StringSchema::generate()->setName('translationId'))
                ->addSchema(StringSchema::generate()->setName('defaultText'))
                ->addSchema(MapSchema::generateStringMap()
                    ->setName('placeholders')
                    ->makeNullable())
        );
    }
}