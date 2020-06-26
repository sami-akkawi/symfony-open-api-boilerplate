<?php declare(strict_types=1);

namespace App\ApiV1Bundle\Parameter;

use App\OpenApiSpecification\ApiComponents\ComponentsParameter\Parameter;
use App\OpenApiSpecification\ApiComponents\ComponentsParameter\StringParameter;

final class PaginationOrderDirection extends AbstractParameter
{
    protected static function getOpenApiResponseWithoutName(): Parameter
    {
        return StringParameter::generateInQuery('orderDirection')
            ->setDescription('The direction of order: ASC or DESC.')
            ->setOptions(['ASC', 'DESC'])
            ->makeNullable();
    }
}