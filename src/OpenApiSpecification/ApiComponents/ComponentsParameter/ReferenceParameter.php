<?php declare(strict_types=1);

namespace App\OpenApiSpecification\ApiComponents\ComponentsParameter;

use App\OpenApiSpecification\ApiComponents\ComponentsParameter;
use App\OpenApiSpecification\ApiComponents\ComponentsParameter\Parameter\ParameterKey;
use App\OpenApiSpecification\ApiComponents\ComponentsParameter\Parameter\ParameterName;
use App\OpenApiSpecification\ApiComponents\Reference;
use App\OpenApiSpecification\ApiComponents\Schema;

final class ReferenceParameter extends ComponentsParameter
{
    private Reference $reference;
    private Parameter $parameter;

    private function __construct(Reference $reference, Parameter $parameter, ?ParameterKey $docName = null)
    {
        $this->reference = $reference;
        $this->parameter = $parameter;
        $this->key = $docName;
    }

    public static function generate(string $objectName, Parameter $parameter): self
    {
        return new self(Reference::generateParameterReference($objectName), $parameter);
    }

    public function setKey(string $key): self
    {
        return new self($this->reference, $this->parameter, ParameterKey::fromString($key));
    }

    public function toParameter(): Parameter
    {
        return $this->parameter;
    }

    public function toOpenApiSpecification(): array
    {
        return $this->reference->toOpenApiSpecification();
    }

    public function getName(): ParameterName
    {
        return $this->parameter->getName();
    }

    public function isRequired(): bool
    {
        return $this->parameter->isRequired();
    }

    public function isValueValid($value): array
    {
        return $this->parameter->isValueValid($value);
    }

    public function getSchema(): Schema
    {
        return $this->parameter->getSchema();
    }
}