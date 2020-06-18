<?php declare(strict=1);

namespace App\OpenApiSpecification\ApiComponents;

use App\OpenApiSpecification\ApiComponents\RequestBody\DetailedRequestBody;
use App\OpenApiSpecification\ApiComponents\RequestBody\RequestBodyName;

abstract class RequestBody
{
    protected ?RequestBodyName $name;

    public abstract function setName(string $name);

    public function hasName(): bool
    {
        return (bool)$this->name;
    }

    public function getName(): RequestBodyName
    {
        return $this->name;
    }

    public abstract function toDetailedRequestBody(): DetailedRequestBody;

    public abstract function toOpenApiSpecification(): array;
}