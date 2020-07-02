<?php declare(strict_types=1);

namespace App\ApiV1Bundle\Header;

use App\OpenApiSpecification\ApiComponents\ComponentsHeader\DetailedHeader;
use App\OpenApiSpecification\ApiComponents\ComponentsHeader\ReferenceHeader;

abstract class AbstractHeader
{
    public static function getOpenApiHeader(): DetailedHeader
    {
        return static::getOpenApiHeaderWithoutName()->setKey(static::getClassName());
    }

    abstract protected static function getOpenApiHeaderWithoutName(): DetailedHeader;

    public static function getReferenceParameter(): ReferenceHeader
    {
        return ReferenceHeader::generate(static::getClassName(), static::getOpenApiHeaderWithoutName());
    }

    public static function getClassName(): string
    {
        $path = explode('\\', static::class);
        return array_pop($path);
    }
}