<?php declare(strict=1);
// Created by sami-akkawi on 19.05.20

namespace App\ApiV1Bundle\ApiSpecification\ApiComponents;

use App\ApiV1Bundle\ApiSpecification\ApiComponents\ComponentsResponse\Response\ResponseHttpCode;

/**
 * Describes a single response from an API Operation, including design-time, static links to operations based on the
 * response.
 * https://swagger.io/specification/#response-object
 */
abstract class Response
{
    protected ResponseHttpCode $code;

    public function getCode(): ResponseHttpCode
    {
        return $this->code;
    }

    public abstract function toOpenApiSpecification(): array;
}