<?php declare(strict=1);

namespace App\ApiV1Bundle\Response;

use App\ApiV1Bundle\ApiSpecification\ApiComponents\Response\DetailedResponse;
use App\ApiV1Bundle\ApiSpecification\ApiComponents\Schema\ArraySchema;
use App\ApiV1Bundle\ApiSpecification\ApiComponents\Schema\ObjectSchema;
use App\ApiV1Bundle\ApiSpecification\ApiComponents\Schema\StringSchema;
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
                        ->addSchema(
                            StringSchema::generate()
                                ->setName('errorType')
                        )
                )->setName('data'))
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