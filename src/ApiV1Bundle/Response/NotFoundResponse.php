<?php declare(strict_types=1);

namespace App\ApiV1Bundle\Response;

use App\OpenApiSpecification\ApiComponents\Response\DetailedResponse;
use App\OpenApiSpecification\ApiComponents\Schema\ArraySchema;
use App\OpenApiSpecification\ApiComponents\Schema\ObjectSchema;
use App\OpenApiSpecification\ApiComponents\Schema\StringSchema;
use App\OpenApiSpecification\ApiComponents\Schemas;
use App\ApiV1Bundle\Schema\FieldMessage;
use App\ApiV1Bundle\Schema\Message;

final class NotFoundResponse extends AbstractResponse
{
    protected static function getOpenApiResponseWithoutName(): DetailedResponse
    {
        return DetailedResponse::generateNotFoundJson(
            ObjectSchema::generate(
                Schemas::generate()
                ->addSchema(ObjectSchema::generate(
                    Schemas::generate()
                        ->addSchema(StringSchema::generate()->setName('errorType'))
                )
                    ->setName('data'))
                ->addSchema(ArraySchema::generate(Message::getReferenceSchema())
                    ->setName('messages')
                    ->makeValuesUnique()
                    ->makeNullable())
                ->addSchema(ArraySchema::generate(FieldMessage::getReferenceSchema())
                    ->setName('fieldMessages')
                    ->makeValuesUnique()
                    ->makeNullable())
            )
        );
    }
}