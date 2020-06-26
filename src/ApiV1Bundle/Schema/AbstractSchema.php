<?php declare(strict_types=1);

namespace App\ApiV1Bundle\Schema;

use App\OpenApiSpecification\ApiComponents\Schema\Schema;
use App\OpenApiSpecification\ApiComponents\Schema\ReferenceSchema;

abstract class AbstractSchema
{
    public abstract function toDetailedSchema(): Schema;

    public static function getOpenApiSchema(): Schema
    {
        return static::getOpenApiSchemaWithoutName()->setName(static::getClassName());
    }

    protected abstract static function getOpenApiSchemaWithoutName(): Schema;

    public static function getReferenceSchema(): ReferenceSchema
    {
        return ReferenceSchema::generate(static::getClassName(), static::getOpenApiSchemaWithoutName());
    }

    public abstract static function getAlwaysRequiredFields(): array;

    public abstract function requireOnly(array $fieldNames);

    private static function getClassName(): string
    {
        $path = explode('\\', static::class);
        return str_replace('Schema', '', array_pop($path));
    }
}