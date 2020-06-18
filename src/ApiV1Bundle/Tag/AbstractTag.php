<?php declare(strict_types=1);

namespace App\ApiV1Bundle\Tag;

use App\OpenApiSpecification\ApiTag;

abstract class AbstractTag
{
    public abstract static function getApiTag(): ApiTag;
}