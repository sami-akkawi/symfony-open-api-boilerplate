<?php declare(strict=1);

namespace App\ApiV1Bundle\Response;

use App\ApiV1Bundle\ApiSpecification\ApiComponents\ComponentsResponse\DetailedResponse;
use App\ApiV1Bundle\ApiSpecification\ApiComponents\ComponentsSchema\ArraySchema;
use App\ApiV1Bundle\ApiSpecification\ApiComponents\ComponentsSchema\ObjectSchema;
use App\ApiV1Bundle\ApiSpecification\ApiComponents\ComponentsSchema\Schema\SchemaName;
use App\ApiV1Bundle\ApiSpecification\ApiComponents\ComponentsSchema\StringSchema;
use App\ApiV1Bundle\ApiSpecification\ApiComponents\Response;
use App\ApiV1Bundle\ApiSpecification\ApiComponents\Schemas;
use App\ApiV1Bundle\Schema\FieldMessage;
use App\ApiV1Bundle\Schema\Message;

final class NotFoundResponse extends AbstractResponse
{
    protected static function getOpenApiResponseWithoutName(): Response
    {
        return DetailedResponse::generateNotFoundJson(
            ObjectSchema::generate(
                Schemas::generate()
                ->addSchema(ObjectSchema::generate(
                    Schemas::generate()
                    ->addSchema(StringSchema::generate('errorType')), 'data'
                ))
                ->addSchema(ArraySchema::generateWithUniqueValues(Message::getReferenceSchema())
                    ->setName(SchemaName::fromString('messages'))->makeNullable())
                ->addSchema(ArraySchema::generateWithUniqueValues(FieldMessage::getReferenceSchema())
                    ->setName(SchemaName::fromString('fieldMessages'))->makeNullable())
            )
        );
    }
}