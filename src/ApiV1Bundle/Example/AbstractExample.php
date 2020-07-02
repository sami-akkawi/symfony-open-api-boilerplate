<?php declare(strict_types=1);

namespace App\ApiV1Bundle\Example;

use App\OpenApiSpecification\ApiComponents\ComponentsExample\Example;
use App\OpenApiSpecification\ApiComponents\ComponentsExample\ReferenceExample;

abstract class AbstractExample
{
    public static function getOpenApiExample(): Example
    {
        return static::getOpenApiExampleWithoutName()->setName(static::getClassName());
    }

    abstract protected static function getOpenApiExampleWithoutName(): Example;

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