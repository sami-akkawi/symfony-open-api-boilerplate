<?php declare(strict=1);

namespace App\ApiV1Bundle\Parameter;

use App\OpenApiSpecification\ApiComponents\Parameter\DetailedParameter;
use App\OpenApiSpecification\ApiComponents\Parameter\StringParameter;

final class PaginationOrderBy extends AbstractParameter
{
    protected static function getOpenApiResponseWithoutName(): DetailedParameter
    {
        return StringParameter::generateInQuery('orderBy')
            ->setDescription('The column name for the information to be ordered by.')
            ->makeNullable();
    }
}