<?php declare(strict_types=1);

namespace App\OpenApiSpecification\ApiComponents;

use App\OpenApiSpecification\ApiComponents\Parameter\DetailedParameter;
use App\OpenApiSpecification\ApiException\SpecificationException;

final class Parameters
{
    /** @var Parameter[] */
    private array $parameters;

    private function __construct(array $parameters)
    {
        $this->parameters = $parameters;
    }

    public static function generate(): self
    {
        return new self([]);
    }

    private function hasParameter(Parameter $parameter): bool
    {
        foreach ($this->parameters as $thisParameter) {
            $thisParameter = $thisParameter->toDetailedParameter();
            $thatParameter = $parameter->toDetailedParameter();

            if ($thisParameter->isIdenticalTo($thatParameter)) {
                return true;
            }
        }
        return false;
    }

    public function addParameter(Parameter $parameter): self
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
            if (!$parameter->hasDocName()) {
                throw SpecificationException::generateMustHaveKeyInComponents();
            }
            $parameters[$parameter->getDocName()->toString()] = $parameter->toOpenApiSpecification();
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

    public function getPathParameter(string $name): ?DetailedParameter
    {
        foreach ($this->parameters as $parameter) {
            $detailedParameter = $parameter->toDetailedParameter();
            if (
                $detailedParameter->isInPath()
                && $detailedParameter->getName()->toString() === $name
            ) {
                return $detailedParameter;
            }
        }

        return null;
    }

    /** @return Parameter[] */
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

    /** @return Parameter[] */
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

    /** @return Parameter[] */
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

    /** @return Parameter[] */
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