<?php declare(strict=1);

namespace App\ApiV1Bundle\Schema;

use App\ApiV1Bundle\ApiSpecification\ApiComponents\Schema\MapSchema;
use App\ApiV1Bundle\ApiSpecification\ApiComponents\Schema\ObjectSchema;
use App\ApiV1Bundle\ApiSpecification\ApiComponents\Schema\Schema\SchemaType;
use App\ApiV1Bundle\ApiSpecification\ApiComponents\Schema\StringSchema;
use App\ApiV1Bundle\ApiSpecification\ApiComponents\Schema;
use App\ApiV1Bundle\ApiSpecification\ApiComponents\Schemas;

final class Message extends AbstractSchema
{
    protected static function getOpenApiSchemaWithoutName(): Schema
    {
        return ObjectSchema::generate(
            Schemas::generate()
                ->addSchema(StringSchema::generate('id')
                    ->setFormat(SchemaType::STRING_UUID_FORMAT))
                ->addSchema(StringSchema::generate('type')
                    ->setOptions(['info', 'success', 'warning', 'error']))
                ->addSchema(StringSchema::generate('translationId'))
                ->addSchema(StringSchema::generate('defaultText'))
                ->addSchema(MapSchema::generateStringMap('placeholders')
                    ->makeNullable())
        );
    }
}