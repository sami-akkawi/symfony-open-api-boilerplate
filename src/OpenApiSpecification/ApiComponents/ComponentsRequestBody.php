<?php declare(strict_types=1);

namespace App\OpenApiSpecification\ApiComponents;

use App\OpenApiSpecification\ApiComponents\ComponentsRequestBody\RequestBody;
use App\OpenApiSpecification\ApiComponents\ComponentsRequestBody\RequestBody\RequestBodyName;

abstract class ComponentsRequestBody
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

    public abstract function getDefinedMimeTypes(): array;

    public abstract function isValueValidByMimeType(string $mimeType, $value): array;

    public abstract function toRequestBody(): RequestBody;

    public abstract function toOpenApiSpecification(): array;
}