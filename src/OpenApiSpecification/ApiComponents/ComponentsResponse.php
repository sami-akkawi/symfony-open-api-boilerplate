<?php declare(strict_types=1);

namespace App\OpenApiSpecification\ApiComponents;

use App\OpenApiSpecification\ApiComponents\ComponentsResponse\ResponseSchema;
use App\OpenApiSpecification\ApiComponents\ComponentsResponse\Response\ResponseDescription;
use App\OpenApiSpecification\ApiComponents\ComponentsResponse\Response\ResponseHttpCode;
use App\OpenApiSpecification\ApiComponents\ComponentsResponse\Response\ResponseName;

/**
 * Describes a single response from an API Operation, including design-time, static links to operations based on the
 * response.
 * http://spec.openapis.org/oas/v3.0.3#response-object
 */

abstract class ComponentsResponse
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

    public abstract function toResponse(): ResponseSchema;

    public abstract function getDefinedMimeTypes(): array;

    public abstract function isValueValidByMimeType(string $mimeType, $value): array;

    public abstract function getDescription(): ResponseDescription;
}