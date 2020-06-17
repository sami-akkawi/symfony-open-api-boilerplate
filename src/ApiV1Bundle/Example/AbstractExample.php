<?php declare(strict=1);

namespace App\ApiV1Bundle\Example;

use App\ApiV1Bundle\ApiSpecification\ApiComponents\Example\DetailedExample;
use App\ApiV1Bundle\ApiSpecification\ApiComponents\Example\ReferenceExample;

abstract class AbstractExample
{
    public static function getOpenApiExample(): DetailedExample
    {
        return static::getOpenApiExampleWithoutName()->setName(static::getClassName());
    }

    protected abstract static function getOpenApiExampleWithoutName(): DetailedExample;

    public static function getReferenceExample(): ReferenceExample
    {
        return ReferenceExample::generate(static::getClassName(), static::getOpenApiExampleWithoutName());
    }

    public static function getClassName(): string
    {
        $path = explode('\\', static::class);
        return array_pop($path);
    }
}