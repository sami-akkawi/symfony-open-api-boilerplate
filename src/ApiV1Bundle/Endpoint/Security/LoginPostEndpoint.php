<?php declare(strict_types=1);

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
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Uid\Uuid;

final class LoginPostEndpoint extends AbstractPostEndpoint
{

    protected function subHandle(array $pathParams, array $requestBody, array $queryParams, array $headerParams, array $cookieParams): Response
    {
        $data = [
            'id' => Uuid::v4()->toRfc4122(),
            'email' => 'someemail@example.com',
            'username' => 'mockeUsername'
        ];

        return new Response(json_encode($data));
    }

    public static function getPartialPath(): PathPartialUrl
    {
        return PathPartialUrl::fromString('login');
    }

    public static function getRequestBody(): ?RequestBody
    {
        return DetailedRequestBody::generate(
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
        )->require();
    }

    protected static function getResponses(): Responses
    {
        return Responses::generate()->addResponse(
            DetailedResponse::generateOk(
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