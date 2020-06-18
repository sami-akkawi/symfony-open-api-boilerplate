<?php declare(strict=1);

namespace App\ApiV1Bundle\Parameter;

use App\OpenApiSpecification\ApiComponents\Parameter\DetailedParameter;
use App\OpenApiSpecification\ApiComponents\Parameter\ReferenceParameter;

abstract class AbstractParameter
{
    public static function getOpenApiParameter(): DetailedParameter
    {
        return static::getOpenApiResponseWithoutName()->setDocName(static::getClassName());
    }

    protected abstract static function getOpenApiResponseWithoutName(): DetailedParameter;

    public static function getReferenceParameter(): ReferenceParameter
    {
        return ReferenceParameter::generate(static::getClassName(), static::getOpenApiResponseWithoutName());
    }

    public static function getClassName(): string
    {
        $path = explode('\\', static::class);
        return array_pop($path);
    }
}