<?php declare(strict_types=1);

namespace App\OpenApiSpecification\ApiComponents;

use App\OpenApiSpecification\ApiComponents\RequestBody\RequestBodyName;
use App\OpenApiSpecification\ApiException\SpecificationException;

final class RequestBodies
{
    /** @var RequestBody[] */
    private array $requestBodies;

    private function __construct(array $requestBodies)
    {
        $this->requestBodies = $requestBodies;
    }

    public static function generate(): self
    {
        return new self([]);
    }

    public function hasSchema(RequestBodyName $name): bool
    {
        foreach ($this->requestBodies as $requestBody) {
            if ($requestBody->getName()->isIdenticalTo($name)) {
                return true;
            }
        }

        return false;
    }

    public function addRequestBody(RequestBody $requestBody): self
    {
        if (!$requestBody->hasName()) {
            throw SpecificationException::generateRequestBodyInRequestBodiesNeedsAName();
        }
        if ($this->hasSchema($requestBody->getName())) {
            throw SpecificationException::generateDuplicateDefinitionException($requestBody->getName()->toString());
        }
        return new self(array_merge($this->requestBodies, [$requestBody]));
    }

    public function toOpenApiSpecification(): array
    {
        $requestBodies = [];
        foreach ($this->requestBodies as $requestBody) {
            $requestBodies[$requestBody->getName()->toString()] = $requestBody->toOpenApiSpecification();
        }
        ksort($requestBodies);
        return $requestBodies;
    }

    public function isDefined(): bool
    {
        return (bool)count($this->requestBodies);
    }
}