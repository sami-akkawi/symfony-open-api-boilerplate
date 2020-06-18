<?php declare(strict=1);

namespace App\OpenApiSpecification\ApiComponents;

use App\OpenApiSpecification\ApiComponents\Parameter\DetailedParameter;
use App\OpenApiSpecification\ApiComponents\Parameter\ParameterDocName;

/**
 * Describes a single operation parameter.
 * A unique parameter is defined by a combination of a name and location.
 * http://spec.openapis.org/oas/v3.0.3#parameter-object
 */

abstract class Parameter
{
    protected ?ParameterDocName $docName;

    public abstract function setDocName(string $name);

    public abstract function toDetailedParameter(): DetailedParameter;

    public function hasDocName(): bool
    {
        return (bool)$this->docName;
    }

    public function getDocName(): ?ParameterDocName
    {
        return $this->docName;
    }

    public abstract function toOpenApiSpecification(): array;
}