<?php declare(strict=1);

namespace App\ApiV1Bundle\Schema;

use App\ApiV1Bundle\ApiSpecification\ApiComponents\Schema\ArraySchema;
use App\ApiV1Bundle\ApiSpecification\ApiComponents\Schema\DiscriminatorSchema;
use App\ApiV1Bundle\ApiSpecification\ApiComponents\Schema\IntegerSchema;
use App\ApiV1Bundle\ApiSpecification\ApiComponents\Schema\ObjectSchema;
use App\ApiV1Bundle\ApiSpecification\ApiComponents\Schema\StringSchema;
use App\ApiV1Bundle\ApiSpecification\ApiComponents\Schema\DetailedSchema;
use App\ApiV1Bundle\ApiSpecification\ApiComponents\Schemas;

final class FieldMessage extends AbstractSchema
{
    protected static function getOpenApiSchemaWithoutName(): DetailedSchema
    {
        return ObjectSchema::generate(
            Schemas::generate()->addSchema(
                ArraySchema::generate(
                    DiscriminatorSchema::generateAnyOf()
                    ->addSchema(StringSchema::generate())
                    ->addSchema(IntegerSchema::generate())
                )->setName('path')
            )->addSchema(Message::getReferenceSchema()->setName('message'))
        );
    }
}