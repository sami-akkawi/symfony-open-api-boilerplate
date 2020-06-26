<?php declare(strict_types=1);

namespace App\ApiV1Bundle\Parameter;

use App\OpenApiSpecification\ApiComponents\ComponentsParameter\Parameter;
use App\OpenApiSpecification\ApiComponents\ComponentsParameter\ReferenceParameter;

abstract class AbstractParameter
{
    public static function getOpenApiParameter(): Parameter
    {
        return static::getOpenApiResponseWithoutName()->setKey(static::getClassName());
    }

    protected abstract static function getOpenApiResponseWithoutName(): Parameter;

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