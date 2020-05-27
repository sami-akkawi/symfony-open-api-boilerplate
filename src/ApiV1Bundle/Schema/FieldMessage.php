<?php declare(strict=1);

namespace App\ApiV1Bundle\Schema;

use App\ApiV1Bundle\ApiSpecification\ApiComponents\ComponentsSchema\ArraySchema;
use App\ApiV1Bundle\ApiSpecification\ApiComponents\ComponentsSchema\DiscriminatorSchema;
use App\ApiV1Bundle\ApiSpecification\ApiComponents\ComponentsSchema\IntegerSchema;
use App\ApiV1Bundle\ApiSpecification\ApiComponents\ComponentsSchema\ObjectSchema;
use App\ApiV1Bundle\ApiSpecification\ApiComponents\ComponentsSchema\Schema\SchemaName;
use App\ApiV1Bundle\ApiSpecification\ApiComponents\ComponentsSchema\StringSchema;
use App\ApiV1Bundle\ApiSpecification\ApiComponents\Schema;
use App\ApiV1Bundle\ApiSpecification\ApiComponents\Schemas;

final class FieldMessage extends AbstractSchema
{
    protected static function getOpenApiSchemaWithoutName(): Schema
    {
        return ObjectSchema::generate(
            Schemas::generate()->addSchema(
                ArraySchema::generateWithUniqueValues(
                    DiscriminatorSchema::generateAnyOf('path')
                    ->addSchema(StringSchema::generate())
                    ->addSchema(IntegerSchema::generate())
                )->setName(SchemaName::fromString('path'))
            )->addSchema(Message::getReferenceSchema('message'))
        );
    }
}