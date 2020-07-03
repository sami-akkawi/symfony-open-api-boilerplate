<?php declare(strict_types=1);

namespace App\OpenApiSpecification\ApiComponents;

use App\OpenApiSpecification\ApiComponents\ComponentsLink\Link;
use App\OpenApiSpecification\ApiComponents\ComponentsLink\Link\LinkKey;
use App\OpenApiSpecification\ApiComponents\ComponentsLink\Link\LinkOperationId;

abstract class ComponentsLink
{
    abstract public function toLink(): Link;

    abstract public function getOperationId(): LinkOperationId;

    abstract public function toOpenApiSpecification(): array;

    abstract public function getKey(): ?LinkKey;
}