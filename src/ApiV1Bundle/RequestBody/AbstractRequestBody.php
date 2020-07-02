<?php declare(strict_types=1);

namespace App\ApiV1Bundle\RequestBody;

use App\OpenApiSpecification\ApiComponents\ComponentsRequestBody\RequestBody;
use App\OpenApiSpecification\ApiComponents\ComponentsRequestBody\ReferenceRequestBody;

abstract class AbstractRequestBody
{
    public static function getOpenApiRequestBody(): RequestBody
    {
        return static::getOpenApiRequestBodyWithoutName()->setName(static::getClassName());
    }

    abstract protected static function getOpenApiRequestBodyWithoutName(): RequestBody;

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