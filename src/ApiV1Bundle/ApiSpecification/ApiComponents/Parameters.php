<?php declare(strict=1);

namespace App\ApiV1Bundle\ApiSpecification\ApiComponents;

use App\ApiV1Bundle\ApiSpecification\ApiException\SpecificationException;

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
            if ($thisParameter->isIdenticalTo($parameter)) {
                return true;
            }
        }
        return false;
    }

    public function addParameter(Parameter $parameter): self
    {
        if ($this->hasParameter($parameter)) {
            throw SpecificationException::generateDuplicateSecurityRequirementsException();
        }

        return new self(array_merge($this->parameters, [$parameter]));
    }

    public function toOpenApiSpecificationForEndpoint(): array
    {
        if (!$this->isDefined()) {
            throw SpecificationException::generateResponsesMustBeDefined();
        }
        $responses = [];
        foreach ($this->parameters as $parameter) {
            $responses[] = $parameter->toOpenApiSpecification();
        }
        return $responses;
    }

    public function toOpenApiSpecificationForComponents(bool $sorted = true): array
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
        if ($sorted) {
            ksort($parameters);
        }
        return $parameters;
    }

    public function isDefined(): bool
    {
        return (bool)count($this->parameters);
    }
}