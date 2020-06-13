<?php declare(strict=1);

namespace App\ApiV1Bundle\ApiSpecification\ApiComponents;

use App\ApiV1Bundle\ApiSpecification\ApiComponents\Response\Response\ResponseHttpCode;
use App\ApiV1Bundle\ApiSpecification\ApiComponents\Response\Response\ResponseName;
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

    private function hasKey(ResponseName $key): bool
    {
        foreach ($this->responses as $response) {
            if ($response->hasName() && $response->getName()->isIdenticalTo($key)) {
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
        if ($response->hasName() && $this->hasKey($response->getName())) {
            throw SpecificationException::generateDuplicateDefinitionException($response->getName()->toString());
        }
        return new self(array_merge($this->responses, [$response]));
    }

    public function toOpenApiSpecificationForComponents(): array
    {
        if (!$this->isDefined()) {
            throw SpecificationException::generateResponsesMustBeDefined();
        }
        $responses = [];
        foreach ($this->responses as $response) {
            if (!$response->hasName()) {
                throw SpecificationException::generateMustHaveKeyInComponents();
            }
            $responses[$response->getName()->toString()] = $response->toOpenApiSpecification();
        }
        ksort($responses);
        return $responses;
    }

    public function isDefined(): bool
    {
        return (bool)count($this->responses);
    }
}