<?php declare(strict=1);

namespace App\ApiV1Bundle\RequestBody;

use App\OpenApiSpecification\ApiComponents\RequestBody\DetailedRequestBody;
use App\OpenApiSpecification\ApiComponents\RequestBody\ReferenceRequestBody;

abstract class AbstractRequestBody
{
    public static function getOpenApiRequestBody(): DetailedRequestBody
    {
        return static::getOpenApiRequestBodyWithoutName()->setName(static::getClassName());
    }

    protected abstract static function getOpenApiRequestBodyWithoutName(): DetailedRequestBody;

    public static function getReferenceRequestBody(): ReferenceRequestBody
    {
        return ReferenceRequestBody::generate(static::getClassName(), static::getOpenApiRequestBodyWithoutName());
    }

    private static function getClassName(): string
    {
        $path = explode('\\', static::class);
        return array_pop($path);
    }
}