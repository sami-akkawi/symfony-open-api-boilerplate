<?php declare(strict_types=1);

namespace App\ApiV1Bundle\Parameter;

use App\OpenApiSpecification\ApiComponents\ComponentsParameter\Parameter;
use App\OpenApiSpecification\ApiComponents\ComponentsParameter\IntegerParameter;

final class PaginationPerPage extends AbstractParameter
{
    protected static function getOpenApiResponseWithoutName(): Parameter
    {
        return IntegerParameter::generateInQuery('perPage')
            ->setDescription('Number of entries to show on one page.')
            ->setMinimum(0)
            ->makeNullable();
    }
}