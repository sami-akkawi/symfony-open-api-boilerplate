<?php declare(strict=1);

namespace App\ApiV1Bundle\Parameter;

use App\ApiV1Bundle\ApiSpecification\ApiComponents\Parameter\DetailedParameter;
use App\ApiV1Bundle\ApiSpecification\ApiComponents\Parameter\IntegerParameter;

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