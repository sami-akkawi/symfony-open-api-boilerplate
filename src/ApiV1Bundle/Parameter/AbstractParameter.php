<?php declare(strict=1);

namespace App\ApiV1Bundle\Parameter;

use App\ApiV1Bundle\ApiSpecification\ApiComponents\Parameter\DetailedParameter;
use App\ApiV1Bundle\ApiSpecification\ApiComponents\Parameter\ReferenceParameter;

abstract class AbstractParameter
{
    public static function getOpenApiParameter(): DetailedParameter
    {
        return static::getOpenApiResponseWithoutName()->setDocName(static::getClassName());
    }

    protected abstract static function getOpenApiResponseWithoutName(): DetailedParameter;

    public static function getReferenceResponse(): ReferenceParameter
    {
        return ReferenceParameter::generate(static::getClassName());
    }

    public static function getClassName(): string
    {
        $path = explode('\\', static::class);
        return array_pop($path);
    }
}