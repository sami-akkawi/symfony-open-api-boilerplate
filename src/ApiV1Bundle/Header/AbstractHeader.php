<?php declare(strict=1);

namespace App\ApiV1Bundle\Header;

use App\OpenApiSpecification\ApiComponents\Header\DetailedHeader;
use App\OpenApiSpecification\ApiComponents\Header\ReferenceHeader;

abstract class AbstractHeader
{
    public static function getOpenApiHeader(): DetailedHeader
    {
        return static::getOpenApiHeaderWithoutName()->setDocName(static::getClassName());
    }

    protected abstract static function getOpenApiHeaderWithoutName(): DetailedHeader;

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