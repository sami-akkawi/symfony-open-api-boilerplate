<?php declare(strict=1);

namespace App\ApiV1Bundle\Response;

use App\ApiV1Bundle\ApiSpecification\ApiComponents\ComponentsResponse\ReferenceResponse;
use App\ApiV1Bundle\ApiSpecification\ApiComponents\ComponentsResponse\Response\ResponseKey;
use App\ApiV1Bundle\ApiSpecification\ApiComponents\Response;

abstract class AbstractResponse
{
    public static function getOpenApiResponse(): Response
    {
        return static::getOpenApiResponseWithoutName()->setKey(ResponseKey::fromString(static::getClassName()));
    }

    protected abstract static function getOpenApiResponseWithoutName(): Response;

    public static function getReferenceResponse(): ReferenceResponse
    {
        return ReferenceResponse::generate(static::getHttpCode(), static::getClassName());
    }

    public static function getClassName(): string
    {
        $path = explode('\\', static::class);
        return array_pop($path);
    }

    private static function getHttpCode(): string
    {
        return static::getOpenApiResponseWithoutName()->getCode()->toString();
    }
}