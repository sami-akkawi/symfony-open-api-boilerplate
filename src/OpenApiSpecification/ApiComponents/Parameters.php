<?php declare(strict_types=1);

namespace App\OpenApiSpecification\ApiComponents;

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
}