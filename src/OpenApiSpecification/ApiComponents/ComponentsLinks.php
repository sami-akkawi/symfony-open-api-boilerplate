<?php declare(strict_types=1);

namespace App\OpenApiSpecification\ApiComponents;

use App\OpenApiSpecification\ApiComponents\ComponentsLink\Link\LinkOperationId;
use App\OpenApiSpecification\ApiException\SpecificationException;

final class ComponentsLinks
{
    /** @var ComponentsLink[] */
    private array $links;

    private function __construct(array $links)
    {
        $this->links = $links;
    }

    public static function generate(): self
    {
        return new self([]);
    }

    public function toArrayOfLinks(): array
    {
        return $this->links;
    }

    public function hasLink(LinkOperationId $operationId): bool
    {
        foreach ($this->links as $link) {
            if ($link->getOperationId()->isIdenticalTo($operationId)) {
                return true;
            }
        }

        return false;
    }

    public function addLink(ComponentsLink $link): self
    {
        if ($this->hasLink($link->getOperationId())) {
            throw SpecificationException::generateDuplicateDefinitionException($link->getOperationId()->toString());
        }

        return new self(array_merge($this->links, [$link]));
    }

    public function isDefined(): bool
    {
        return (bool)count($this->links);
    }

    public function toOpenApiSpecificationForEndpoint(): array
    {
        $specification = [];
        foreach ($this->links as $link) {
            $specification[$link->getOperationId()->toString()] = $link->toOpenApiSpecification();
        }
        ksort($specification);
        return $specification;
    }

    public function toOpenApiSpecificationForComponents(): array
    {
        $specification = [];
        foreach ($this->links as $link) {
            $specification[$link->getKey()->toString()] = $link->toOpenApiSpecification();
        }
        ksort($specification);
        return $specification;
    }
}