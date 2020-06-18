<?php declare(strict=1);

namespace App\ApiV1Bundle\Endpoint;

use App\ApiV1Bundle\ApiSpecification\ApiPath\PathOperation\OperationName;

abstract class AbstractPostEndpoint extends AbstractEndpoint
{
    public static function getOperationName(): OperationName
    {
        return OperationName::generatePost();
    }
}