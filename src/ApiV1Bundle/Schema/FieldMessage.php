<?php declare(strict=1);

namespace App\ApiV1Bundle\Schema;

use App\ApiV1Bundle\ApiSpecification\ApiComponents\Schema\ArraySchema;
use App\ApiV1Bundle\ApiSpecification\ApiComponents\Schema\DiscriminatorSchema;
use App\ApiV1Bundle\ApiSpecification\ApiComponents\Schema\IntegerSchema;
use App\ApiV1Bundle\ApiSpecification\ApiComponents\Schema\ObjectSchema;
use App\ApiV1Bundle\ApiSpecification\ApiComponents\Schema\Schema\SchemaName;
use App\ApiV1Bundle\ApiSpecification\ApiComponents\Schema\StringSchema;
use App\ApiV1Bundle\ApiSpecification\ApiComponents\Schema;
use App\ApiV1Bundle\ApiSpecification\ApiComponents\Schemas;

final class FieldMessage extends AbstractSchema
{
    protected static function getOpenApiSchemaWithoutName(): Schema
    {
        return ObjectSchema::generate(
            Schemas::generate()->addSchema(
                ArraySchema::generateWithoutUniqueValues(
                    DiscriminatorSchema::generateAnyOf()
                    ->addSchema(StringSchema::generate())
                    ->addSchema(IntegerSchema::generate())
                )->setName(SchemaName::fromString('path'))
            )->addSchema(Message::getReferenceSchema('message'))
        );
    }
}