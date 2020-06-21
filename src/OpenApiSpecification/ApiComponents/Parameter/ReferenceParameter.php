<?php declare(strict_types=1);

namespace App\OpenApiSpecification\ApiComponents\Parameter;

use App\OpenApiSpecification\ApiComponents\Parameter;
use App\OpenApiSpecification\ApiComponents\Reference;
use App\OpenApiSpecification\ApiComponents\Schema;

final class ReferenceParameter extends Parameter
{
    private Reference $reference;
    private DetailedParameter $parameter;

    private function __construct(Reference $reference, DetailedParameter $parameter, ?ParameterDocName $docName = null)
    {
        $this->reference = $reference;
        $this->parameter = $parameter;
        $this->docName = $docName;
    }

    public static function generate(string $objectName, DetailedParameter $parameter): self
    {
        return new self(Reference::generateParameterReference($objectName), $parameter);
    }

    public function setDocName(string $name): self
    {
        return new self($this->reference, $this->parameter, ParameterDocName::fromString($name));
    }

    public function toDetailedParameter(): DetailedParameter
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