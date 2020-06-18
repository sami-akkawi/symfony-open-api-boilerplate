<?php declare(strict=1);

namespace App\ApiV1Bundle\Endpoint\Security;

use App\ApiV1Bundle\Endpoint\AbstractPostEndpoint;
use App\ApiV1Bundle\Tag\Security;
use App\OpenApiSpecification\ApiComponents\MediaType;
use App\OpenApiSpecification\ApiComponents\RequestBody;
use App\OpenApiSpecification\ApiComponents\RequestBody\DetailedRequestBody;
use App\OpenApiSpecification\ApiComponents\Response\DetailedResponse;
use App\OpenApiSpecification\ApiComponents\Responses;
use App\OpenApiSpecification\ApiComponents\Schema\ObjectSchema;
use App\OpenApiSpecification\ApiComponents\Schema\Schema\SchemaType;
use App\OpenApiSpecification\ApiComponents\Schema\StringSchema;
use App\OpenApiSpecification\ApiComponents\Schemas;
use App\OpenApiSpecification\ApiPath\PathOperation\OperationTags;
use App\OpenApiSpecification\ApiPath\PathPartialUrl;

final class LoginPostEndpoint extends AbstractPostEndpoint
{
    public static function getPartialPath(): PathPartialUrl
    {
        return PathPartialUrl::fromString('login');
    }

    public static function getRequestBody(): ?RequestBody
    {
        return DetailedRequestBody::generate()->addMediaType(
            MediaType::generateJson(
                ObjectSchema::generate(
                    Schemas::generate()
                    ->addSchema(StringSchema::generate()
                        ->setName('email')
                        ->setFormat(SchemaType::STRING_EMAIL_FORMAT)
                        ->require())
                    ->addSchema(StringSchema::generate()
                        ->setName('password')
                        ->setFormat(SchemaType::STRING_PASSWORD_FORMAT)
                        ->require())
                )
            )
        )->require();
    }

    public static function getResponses(): Responses
    {
        return Responses::generate()->addResponse(
            DetailedResponse::generateOkJson(
                ObjectSchema::generateDataSchema(
                    Schemas::generate()
                    ->addSchema(StringSchema::generate()->setName('id')->setFormat(SchemaType::STRING_UUID_FORMAT))
                    ->addSchema(StringSchema::generate()->setName('email')->setFormat(SchemaType::STRING_EMAIL_FORMAT))
                    ->addSchema(StringSchema::generate()->setName('username'))
                )
            )
        );
    }

    public static function getTags(): OperationTags
    {
        return OperationTags::generate()
            ->addTag(Security::getApiTag()->getName()->toString());
    }
}