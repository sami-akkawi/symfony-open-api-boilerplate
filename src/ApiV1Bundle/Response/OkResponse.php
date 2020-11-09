<?php declare(strict_types=1);

namespace App\ApiV1Bundle\Response;

use App\ApiV1Bundle\Schema\OkResponseSchema;
use App\OpenApiSpecification\ApiComponents\ComponentsResponse\ResponseSchema;

final class OkResponse extends AbstractSuccessResponse
{
    public static function generate(
        array $data,
        array $generalMessages = [],
        array $fieldMessages = []
    ): self {
        return new self($data, $generalMessages, $fieldMessages);
    }

    protected static function getOpenApiResponseWithoutName(): ResponseSchema
    {
        return ResponseSchema::generateOk(
            OkResponseSchema::getReferenceSchema()
        );
    }
}