<?php declare(strict=1);

namespace App\ApiV1Bundle\Parameter;

use App\ApiV1Bundle\ApiSpecification\ApiComponents\Parameter\DetailedParameter;
use App\ApiV1Bundle\ApiSpecification\ApiComponents\Parameter\IntegerParameter;

final class PaginationPerPage extends AbstractParameter
{
    protected static function getOpenApiResponseWithoutName(): DetailedParameter
    {
        return IntegerParameter::generateInQuery('perPage')
            ->setDescription('Number of entries to show on one page.')
            ->setMinimum(0)
            ->makeNullable();
    }
}