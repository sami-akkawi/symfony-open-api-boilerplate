<?php declare(strict_types=1);

namespace App\ApiV1Bundle\Endpoint\Security;

use App\ApiV1Bundle\Endpoint\AbstractPostEndpoint;
use App\ApiV1Bundle\Helpers\ApiV1Request;
use App\ApiV1Bundle\Response\AbstractResponse;
use App\ApiV1Bundle\Response\OkResponse;
use App\ApiV1Bundle\Schema\OkResponseSchema;
use App\ApiV1Bundle\Schema\UserSchema;
use App\ApiV1Bundle\Tag\Security;
use App\OpenApiSpecification\ApiComponents\ComponentsRequestBody;
use App\OpenApiSpecification\ApiComponents\ComponentsRequestBody\RequestBody;
use App\OpenApiSpecification\ApiComponents\ComponentsResponse\ResponseSchema;
use App\OpenApiSpecification\ApiComponents\ComponentsResponses;
use App\OpenApiSpecification\ApiComponents\ComponentsSchema\ObjectSchema;
use App\OpenApiSpecification\ApiComponents\ComponentsSchema\Schema\SchemaType;
use App\OpenApiSpecification\ApiComponents\ComponentsSchema\StringSchema;
use App\OpenApiSpecification\ApiComponents\ComponentsSchemas;
use App\OpenApiSpecification\ApiPath\PathOperation\OperationTags;
use App\OpenApiSpecification\ApiPath\PathPartialUrl;
use Symfony\Component\Uid\Uuid;

final class LoginPostEndpoint extends AbstractPostEndpoint
{

    protected function subHandle(ApiV1Request $request): AbstractResponse
    {
        $data = [
            'id' => Uuid::v4()->toRfc4122(),
            'email' => 'someemail@example.com',
            'username' => 'mockUsername'
        ];

        return OkResponse::generate($data);
    }

    public static function getPartialPath(): PathPartialUrl
    {
        return PathPartialUrl::fromString('login');
    }

    public static function getRequestBody(): ?ComponentsRequestBody
    {
        return RequestBody::generate(
            ObjectSchema::generate(
            ComponentsSchemas::generate()
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

    protected static function getResponses(): ComponentsResponses
    {
        return ComponentsResponses::generate()->addResponse(
            ResponseSchema::generateOk(
                OkResponseSchema::getCustom(
                    UserSchema::getReferenceSchema()
                )
            )
        );
    }

    public static function getTags(): OperationTags
    {
        return OperationTags::generate()->addTag(Security::getApiTag());
    }
}