<?php declare(strict_types=1);

namespace App\OpenApiSpecification\ApiComponents\ComponentsHeader;

use App\OpenApiSpecification\ApiComponents\ComponentsHeader;
use App\OpenApiSpecification\ApiComponents\Reference;

final class ReferenceHeader extends ComponentsHeader
{
    private Reference $reference;
    private DetailedHeader $Header;

    private function __construct(Reference $reference, DetailedHeader $Header, ?HeaderKey $key = null)
    {
        $this->reference = $reference;
        $this->Header = $Header;
        $this->key = $key;
    }

    public static function generate(string $objectName, DetailedHeader $Header): self
    {
        return new self(Reference::generateHeaderReference($objectName), $Header);
    }

    public function setKey(string $key): self
    {
        return new self($this->reference, $this->Header, HeaderKey::fromString($key));
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