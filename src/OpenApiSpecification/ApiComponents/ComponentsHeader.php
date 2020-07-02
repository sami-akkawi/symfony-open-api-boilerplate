<?php declare(strict_types=1);

namespace App\OpenApiSpecification\ApiComponents;

use App\OpenApiSpecification\ApiComponents\ComponentsHeader\DetailedHeader;
use App\OpenApiSpecification\ApiComponents\ComponentsHeader\Header\HeaderKey;

abstract class ComponentsHeader
{
    protected ?HeaderKey $key;

    abstract public function setKey(string $key);

    abstract public function toDetailedHeader(): DetailedHeader;

    public function hasKey(): bool
    {
        return (bool)$this->key;
    }

    public function getKey(): ?HeaderKey
    {
        return $this->key;
    }

    abstract public function toOpenApiSpecification(): array;
}