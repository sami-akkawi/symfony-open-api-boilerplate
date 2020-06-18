<?php declare(strict_types=1);

namespace App\ApiV1Bundle\Tag;

use App\OpenApiSpecification\ApiTag;

final class Security extends AbstractTag
{
    public static function getApiTag(): ApiTag
    {
        return ApiTag::generate('Security')
            ->setDescription('Operations related to authentication and authorisation.');
    }
}