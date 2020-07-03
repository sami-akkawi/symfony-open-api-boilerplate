<?php declare(strict_types=1);

namespace App\OpenApiSpecification\ApiComponents\ComponentsLink;

use App\OpenApiSpecification\ApiComponents\ComponentsLink;
use App\OpenApiSpecification\ApiComponents\ComponentsLink\Link\LinkKey;
use App\OpenApiSpecification\ApiComponents\ComponentsLink\Link\LinkOperationId;
use App\OpenApiSpecification\ApiComponents\Reference;

final class ReferenceLink extends ComponentsLink
{
    private Reference $reference;
    private Link $link;

    private function __construct(Reference $reference, Link $link)
    {
        $this->reference = $reference;
        $this->link = $link;
    }

    public static function generate(string $objectName, Link $link): self
    {
        return new self(Reference::generateLinkReference($objectName), $link);
    }

    public function getOperationId(): LinkOperationId
    {
        return $this->link->getOperationId();
    }

    public function toOpenApiSpecification(): array
    {
        return $this->reference->toOpenApiSpecification();
    }

    public function toLink(): Link
    {
        return $this->link;
    }

    public function getKey(): ?LinkKey
    {
        return $this->link->getKey();
    }
}