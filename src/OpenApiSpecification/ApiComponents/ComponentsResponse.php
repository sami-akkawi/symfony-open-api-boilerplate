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

    abstract public function setName(string $name);

    abstract public function toOpenApiSpecification(): array;

    abstract public function toResponse(): ResponseSchema;

    abstract public function getDefinedMimeTypes(): array;

    abstract public function isValueValidByMimeType(string $mimeType, $value): array;

    abstract public function getDescription(): ResponseDescription;
}