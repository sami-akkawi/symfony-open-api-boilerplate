<?php declare(strict=1);

namespace App\ApiV1Bundle\Schema;

use App\ApiV1Bundle\ApiSpecification\ApiComponents\ComponentsSchema\ReferenceSchema;
use App\ApiV1Bundle\ApiSpecification\ApiComponents\ComponentsSchema\Schema\SchemaName;
use App\ApiV1Bundle\ApiSpecification\ApiComponents\Schema;

abstract class AbstractSchema
{
    public static function getOpenApiSchema(): Schema
    {
        return static::getOpenApiSchemaWithoutName()->setName(SchemaName::fromString(static::getClassName()));
    }

    protected abstract static function getOpenApiSchemaWithoutName(): Schema;

    public static function getReferenceSchema(?string $name = null): ReferenceSchema
    {
        $path = explode('\\', static::class);
        $className = array_pop($path);
        if ($name) {
            return ReferenceSchema::generateWithName($className, $name);
        }
        return ReferenceSchema::generateWithNoName($className);
    }

    private static function getClassName(): string
    {
        $path = explode('\\', static::class);
        return array_pop($path);
    }
}