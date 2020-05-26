<?php declare(strict=1);

namespace App\ApiV1Bundle\Schema;

use App\ApiV1Bundle\ApiSpecification\ApiComponents\ComponentsSchema\ReferenceSchema;
use App\ApiV1Bundle\ApiSpecification\ApiComponents\Schema;

abstract class AbstractSchema
{
    public abstract static function getOpenApiSchema(): Schema;

    public static function getReferenceSchema(?string $name = null): ReferenceSchema
    {
        $path = explode('\\', static::class);
        $className = array_pop($path);
        if ($name) {
            return ReferenceSchema::generateWithName($className, $name);
        }
        return ReferenceSchema::generateWithNoName($className);
    }
}