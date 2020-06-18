<?php declare(strict_types=1);

namespace App\OpenApiSpecification\ApiComponents;

use App\OpenApiSpecification\ApiComponents\Response\DetailedResponse;
use App\OpenApiSpecification\ApiComponents\Response\Response\ResponseHttpCode;
use App\OpenApiSpecification\ApiComponents\Response\Response\ResponseName;

/**
 * Describes a single response from an API Operation, including design-time, static links to operations based on the
 * response.
 * http://spec.openapis.org/oas/v3.0.3#response-object
 */

abstract class Response
{
    protected ?ResponseName $name;
    protected ResponseHttpCode $code;

    public function getCode(): ResponseHttpCode
    {
        return $this->code;
    }

    public function hasName(): bool
    {
        return (bool)$this->name;
    }

    public function getName(): ?ResponseName
    {
        return $this->name;
    }

    public abstract function setName(string $name);

    public abstract function toOpenApiSpecification(): array;

    public abstract function toDetailedResponse(): DetailedResponse;
}