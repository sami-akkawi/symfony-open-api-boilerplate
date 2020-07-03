<?php declare(strict_types=1);

namespace App\ApiV1Bundle\Link;

use App\OpenApiSpecification\ApiComponents\ComponentsLink\Link;
use App\OpenApiSpecification\ApiComponents\ComponentsLink\ReferenceLink;

abstract class AbstractLink
{
    public static function getOpenApiLink(): Link
    {
        return static::getOpenApiLinkWithoutName()->setKey(static::getClassName());
    }

    abstract protected static function getOpenApiLinkWithoutName(): Link;

    public static function getReferenceLink(): ReferenceLink
    {
        return ReferenceLink::generate(static::getClassName(), static::getOpenApiLinkWithoutName());
    }

    public static function getClassName(): string
    {
        $path = explode('\\', static::class);
        return array_pop($path);
    }
}