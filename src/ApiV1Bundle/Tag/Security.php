<?php declare(strict=1);

namespace App\ApiV1Bundle\Tag;

use App\ApiV1Bundle\ApiSpecification\ApiTag;

final class Security extends AbstractTag
{
    public static function getApiTag(): ApiTag
    {
        return ApiTag::generate('Security')
            ->setDescription('Operations related to authentication and authorisation.');
    }
}