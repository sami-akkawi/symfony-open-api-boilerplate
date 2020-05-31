<?php declare(strict=1);

namespace App\ApiV1Bundle\ApiSpecification\ApiComponents\Parameter;

use App\ApiV1Bundle\ApiSpecification\ApiComponents\Parameter;
use App\ApiV1Bundle\ApiSpecification\ApiComponents\Reference;

final class ReferenceParameter extends Parameter
{
    private Reference $reference;

    private function __construct(Reference $reference, ?ParameterDocName $docName = null)
    {
        $this->reference = $reference;
        $this->docName = $docName;
    }

    public static function generate(string $objectName): self
    {
        return new self(Reference::generateParameterReference($objectName));
    }

    public function setDocName(string $name): self
    {
        return new self($this->reference, ParameterDocName::fromString($name));
    }

    public function toOpenApiSpecification(): array
    {
        return $this->reference->toOpenApiSpecification();
    }

    public function getName(): string
    {
        return $this->reference->getStringName();
    }
}