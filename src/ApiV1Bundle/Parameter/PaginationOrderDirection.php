<?php declare(strict=1);

namespace App\ApiV1Bundle\Parameter;

use App\OpenApiSpecification\ApiComponents\Parameter\DetailedParameter;
use App\OpenApiSpecification\ApiComponents\Parameter\StringParameter;

final class PaginationOrderDirection extends AbstractParameter
{
    protected static function getOpenApiResponseWithoutName(): DetailedParameter
    {
        return StringParameter::generateInQuery('orderDirection')
            ->setDescription('The direction of order: ASC or DESC.')
            ->setOptions(['ASC', 'DESC'])
            ->makeNullable();
    }
}