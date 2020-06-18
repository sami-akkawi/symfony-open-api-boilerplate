<?php declare(strict_types=1);

namespace App\ApiV1Bundle\Parameter;

use App\OpenApiSpecification\ApiComponents\Parameter\DetailedParameter;
use App\OpenApiSpecification\ApiComponents\Parameter\IntegerParameter;

final class PaginationCurrentPage extends AbstractParameter
{
    protected static function getOpenApiResponseWithoutName(): DetailedParameter
    {
        return IntegerParameter::generateInQuery('currentPage')
            ->setDescription('Page of data to show.')
            ->setMinimum(1)
            ->makeNullable();
    }
}