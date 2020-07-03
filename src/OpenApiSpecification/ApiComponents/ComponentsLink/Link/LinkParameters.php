<?php declare(strict_types=1);

namespace App\OpenApiSpecification\ApiComponents\ComponentsLink\Link;

use App\OpenApiSpecification\ApiComponents\ComponentsLink\Link\LinkParameter\ParameterKey;
use App\OpenApiSpecification\ApiException\SpecificationException;

final class LinkParameters
{
    /** @var LinkParameter[] */
    private array $parameters;

    private function __construct(array $parameters)
    {
        $this->parameters = $parameters;
    }

    public function toArrayOfLinkParameters(): array
    {
        return $this->parameters;
    }

    public static function generateEmpty(): self
    {
        return new self([]);
    }

    private function hasParameter(ParameterKey $key): bool
    {
        foreach ($this->parameters as $parameter) {
            if ($parameter->getKey()->isIdenticalTo($key)) {
                return true;
            }
        }

        return false;
    }

    public function addParameter(LinkParameter $parameter): self
    {
        if ($this->hasParameter($parameter->getKey())) {
            throw SpecificationException::generateDuplicateDefinitionException($parameter->getKey()->toOpenApiSpecification());
        }

        return new self(array_merge($this->parameters, [$parameter]));
    }

    public function toOpenApiSpecification(): array
    {
        $specification = [];
        foreach ($this->parameters as $parameter) {
            $specification = array_merge($parameter->toOpenApiSpecification());
        }
        ksort($specification);
        return $specification;
    }

    public function isDefined(): bool
    {
        return (bool)count($this->parameters);
    }
}