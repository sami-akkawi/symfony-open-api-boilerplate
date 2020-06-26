<?php declare(strict_types=1);

namespace App\ApiV1Bundle\Parameter;

use App\OpenApiSpecification\ApiComponents\ComponentsParameter\Parameter;
use App\OpenApiSpecification\ApiComponents\ComponentsParameter\StringParameter;

final class PaginationOrderBy extends AbstractParameter
{
    protected static function getOpenApiResponseWithoutName(): Parameter
    {
        return StringParameter::generateInQuery('orderBy')
            ->setDescription('The column name for the information to be ordered by.')
            ->makeNullable();
    }
}