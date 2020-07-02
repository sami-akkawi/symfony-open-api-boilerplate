<?php declare(strict_types=1);

namespace App\ApiV1Bundle\Schema;

use App\OpenApiSpecification\ApiComponents\ComponentsSchema\Schema;
use App\OpenApiSpecification\ApiComponents\ComponentsSchema\ReferenceSchema;

abstract class AbstractSchema
{
    abstract public function toDetailedSchema(): Schema;

    public static function getOpenApiSchema(): Schema
    {
        return static::getOpenApiSchemaWithoutName()->setName(static::getClassName());
    }

    abstract protected static function getOpenApiSchemaWithoutName(): Schema;

    public static function getReferenceSchema(): ReferenceSchema
    {
        return ReferenceSchema::generate(static::getClassName(), static::getOpenApiSchemaWithoutName());
    }

    abstract public static function getAlwaysRequiredFields(): array;

    abstract public function requireOnly(array $fieldNames);

    private static function getClassName(): string
    {
        $path = explode('\\', static::class);
        return str_replace('Schema', '', array_pop($path));
    }
}