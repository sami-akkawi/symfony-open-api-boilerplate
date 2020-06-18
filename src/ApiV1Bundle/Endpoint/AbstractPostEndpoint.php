<?php declare(strict_types=1);

namespace App\ApiV1Bundle\Endpoint;

use App\OpenApiSpecification\ApiPath\PathOperation\OperationName;

abstract class AbstractPostEndpoint extends AbstractEndpoint
{
    public static function getOperationName(): OperationName
    {
        return OperationName::generatePost();
    }
}