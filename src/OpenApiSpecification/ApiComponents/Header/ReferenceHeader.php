<?php declare(strict_types=1);

namespace App\OpenApiSpecification\ApiComponents\Header;

use App\OpenApiSpecification\ApiComponents\Header;
use App\OpenApiSpecification\ApiComponents\Reference;

final class ReferenceHeader extends Header
{
    private Reference $reference;
    private DetailedHeader $Header;

    private function __construct(Reference $reference, DetailedHeader $Header, ?HeaderDocName $docName = null)
    {
        $this->reference = $reference;
        $this->Header = $Header;
        $this->docName = $docName;
    }

    public static function generate(string $objectName, DetailedHeader $Header): self
    {
        return new self(Reference::generateHeaderReference($objectName), $Header);
    }

    public function setDocName(string $name): self
    {
        return new self($this->reference, $this->Header, HeaderDocName::fromString($name));
    }

    public function toDetailedHeader(): DetailedHeader
    {
        return $this->Header;
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