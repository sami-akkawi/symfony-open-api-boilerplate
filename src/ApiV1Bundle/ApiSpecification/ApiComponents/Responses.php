<?php declare(strict=1);

namespace App\ApiV1Bundle\ApiSpecification\ApiComponents;

use App\ApiV1Bundle\ApiSpecification\ApiComponents\ComponentsResponse\Response\ResponseHttpCode;
use App\ApiV1Bundle\ApiSpecification\ApiComponents\ComponentsResponse\Response\ResponseKey;
use App\ApiV1Bundle\ApiSpecification\ApiException\SpecificationException;

final class Responses
{
    /** @var Response[] */
    private array $responses;

    private function __construct(array $responses)
    {
        $this->responses = $responses;
    }

    public static function generate(): self
    {
        return new self([]);
    }

    private function hasHttpCode(ResponseHttpCode $code): bool
    {
        foreach ($this->responses as $response) {
            if ($response->getCode()->isIdenticalTo($code)) {
                return true;
            }
        }
        return false;
    }

    private function hasKey(ResponseKey $key): bool
    {
        foreach ($this->responses as $response) {
            if ($response->hasKey() && $response->getKey()->isIdenticalTo($key)) {
                return true;
            }
        }
        return false;
    }

    public function addResponse(Response $response): self
    {
        if ($this->hasHttpCode($response->getCode())) {
            throw SpecificationException::generateDuplicateDefinitionException($response->getCode()->toString());
        }
        if ($response->hasKey() && $this->hasKey($response->getKey())) {
            throw SpecificationException::generateDuplicateDefinitionException($response->getKey()->toString());
        }
        return new self(array_merge($this->responses, [$response]));
    }

    public function toOpenApiSpecificationForEndpoint(bool $sorted = false): array
    {
        if (!$this->isDefined()) {
            throw SpecificationException::generateResponsesMustBeDefined();
        }
        $responses = [];
        foreach ($this->responses as $response) {
            $responses[$response->getCode()->toString()] = $response->toOpenApiSpecification();
        }
        if ($sorted) {
            ksort($responses);
        }
        return $responses;
    }

    public function toOpenApiSpecificationForComponents(bool $sorted = false): array
    {
        if (!$this->isDefined()) {
            throw SpecificationException::generateResponsesMustBeDefined();
        }
        $responses = [];
        foreach ($this->responses as $response) {
            if (!$response->hasKey()) {
                throw SpecificationException::generateMustHaveKeyInComponents();
            }
            $responses[$response->getKey()->toString()] = $response->toOpenApiSpecification();
        }
        if ($sorted) {
            ksort($responses);
        }
        return $responses;
    }

    public function isDefined(): bool
    {
        return (bool)count($this->responses);
    }
}