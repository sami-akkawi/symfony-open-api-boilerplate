<?php declare(strict_types=1);

namespace App\OpenApiSpecification\ApiComponents;

use App\OpenApiSpecification\ApiComponents\ComponentsParameter\Parameter;
use App\OpenApiSpecification\ApiException\SpecificationException;

final class ComponentsParameters
{
    /** @var ComponentsParameter[] */
    private array $parameters;

    private function __construct(array $parameters)
    {
        $this->parameters = $parameters;
    }

    public static function generate(): self
    {
        return new self([]);
    }

    private function hasParameter(ComponentsParameter $parameter): bool
    {
        foreach ($this->parameters as $thisParameter) {
            $thisParameter = $thisParameter->toParameter();
            $thatParameter = $parameter->toParameter();

            if ($thisParameter->isIdenticalTo($thatParameter)) {
                return true;
            }
        }
        return false;
    }

    public function addParameter(ComponentsParameter $parameter): self
    {
        if ($this->hasParameter($parameter)) {
            throw SpecificationException::generateDuplicateParameters();
        }

        return new self(array_merge($this->parameters, [$parameter]));
    }

    public function toOpenApiSpecificationForRequestContent(): array
    {
        if (!$this->isDefined()) {
            throw SpecificationException::generateResponsesMustBeDefined();
        }
        $parameters = [];
        foreach ($this->parameters as $parameter) {
            if (!$parameter->hasKey()) {
                throw SpecificationException::generateMustHaveKeyInComponents();
            }
            $parameters[$parameter->getKey()->toString()] = $parameter->toOpenApiSpecification();
        }
        return $parameters;
    }

    public function toOpenApiSpecificationForComponents(): array
    {
        $parameters = $this->toOpenApiSpecificationForRequestContent();
        ksort($parameters);
        return $parameters;
    }

    public function toOpenApiSpecificationForEndpoint(): array
    {
        if (!$this->isDefined()) {
            throw SpecificationException::generateResponsesMustBeDefined();
        }
        $parameters = [];
        foreach ($this->parameters as $parameter) {
            $parameters[] = $parameter->toOpenApiSpecification();
        }
        return $parameters;
    }

    public function isDefined(): bool
    {
        return (bool)count($this->parameters);
    }

    public function getPathParameter(string $name): ?Parameter
    {
        foreach ($this->parameters as $parameter) {
            $detailedParameter = $parameter->toParameter();
            if (
                $detailedParameter->isInPath()
                && $detailedParameter->getName()->toString() === $name
            ) {
                return $detailedParameter;
            }
        }

        return null;
    }

    /** @return ComponentsParameter[] */
    public function getAllQueryParameters(): array
    {
        $parameters = [];

        foreach ($this->parameters as $parameter) {
            if ($parameter->isQueryParameter()) {
                $parameters[] = $parameter;
            }
        }

        return $parameters;
    }

    /** @return ComponentsParameter[] */
    public function getAllHeaderParameters(): array
    {
        $parameters = [];

        foreach ($this->parameters as $parameter) {
            if ($parameter->isHeaderParameter()) {
                $parameters[] = $parameter;
            }
        }

        return $parameters;
    }

    /** @return ComponentsParameter[] */
    public function getAllCookieParameters(): array
    {
        $parameters = [];

        foreach ($this->parameters as $parameter) {
            if ($parameter->isCookieParameter()) {
                $parameters[] = $parameter;
            }
        }

        return $parameters;
    }

    /** @return ComponentsParameter[] */
    public function getAllPathParameters(): array
    {
        $parameters = [];

        foreach ($this->parameters as $parameter) {
            if ($parameter->isPathParameter()) {
                $parameters[] = $parameter;
            }
        }

        return $parameters;
    }
}