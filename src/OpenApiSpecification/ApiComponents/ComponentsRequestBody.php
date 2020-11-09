<?php declare(strict_types=1);

namespace App\OpenApiSpecification\ApiComponents;

use App\OpenApiSpecification\ApiComponents\ComponentsRequestBody\RequestBody;
use App\OpenApiSpecification\ApiComponents\ComponentsRequestBody\RequestBody\RequestBodyName;

abstract class ComponentsRequestBody
{
    protected ?RequestBodyName $name;

    abstract public function setName(string $name);

    public function hasName(): bool
    {
        return (bool)$this->name;
    }

    public function getName(): RequestBodyName
    {
        return $this->name;
    }

    abstract public function getDefinedMimeTypes(): array;

    abstract public function isValueValidByMimeType(string $mimeType, $value, array $keysToIgnore): array;

    abstract public function toRequestBody(): RequestBody;

    abstract public function toOpenApiSpecification(): array;
}