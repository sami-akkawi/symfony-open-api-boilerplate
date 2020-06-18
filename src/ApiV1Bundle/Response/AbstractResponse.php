<?php declare(strict_types=1);

namespace App\ApiV1Bundle\Response;

use App\OpenApiSpecification\ApiComponents\Response\ReferenceResponse;
use App\OpenApiSpecification\ApiComponents\Response\DetailedResponse;

abstract class AbstractResponse
{
    public static function getOpenApiResponse(): DetailedResponse
    {
        return static::getOpenApiResponseWithoutName()->setName(static::getClassName());
    }

    protected abstract static function getOpenApiResponseWithoutName(): DetailedResponse;

    public static function getReferenceResponse(): ReferenceResponse
    {
        return ReferenceResponse::generate(static::getClassName(), static::getOpenApiResponseWithoutName());
    }

    public static function getClassName(): string
    {
        $path = explode('\\', static::class);
        return array_pop($path);
    }
}