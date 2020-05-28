<?php declare(strict=1);

namespace App\ApiV1Bundle\ApiSpecification;

use App\ApiV1Bundle\ApiSpecification\ApiComponents\Response;
use App\ApiV1Bundle\ApiSpecification\ApiComponents\Responses;
use App\ApiV1Bundle\ApiSpecification\ApiComponents\Schema;
use App\ApiV1Bundle\ApiSpecification\ApiComponents\Schemas;
use App\ApiV1Bundle\ApiSpecification\ApiComponents\SecurityScheme;
use App\ApiV1Bundle\ApiSpecification\ApiComponents\SecuritySchemes;
use App\ApiV1Bundle\ApiSpecification\ApiException\SpecificationException;

final class ApiComponents
{
    private Schemas $schemas;
    private Responses $responses;
    // todo: parameters
    // todo: examples
    // todo: requestBodies
    // todo: headers
    private SecuritySchemes $securitySchemes;
    // todo: links

    private function __construct(Schemas $schemas, SecuritySchemes $securitySchemes, Responses $responses)
    {
        $this->schemas = $schemas;
        $this->securitySchemes = $securitySchemes;
        $this->responses = $responses;
    }

    public static function generate(): self
    {
        return new self(Schemas::generate(), SecuritySchemes::generate(), Responses::generate());
    }

    public function addSecurityScheme(SecurityScheme $scheme): self
    {
        return new self(
            $this->schemas,
            $this->securitySchemes->addScheme($scheme),
            $this->responses
        );
    }

    public function addSchema(Schema $schema): self
    {
        return new self(
            $this->schemas->addSchema($schema),
            $this->securitySchemes,
            $this->responses
        );
    }

    public function addResponse(Response $response): self
    {
        if (!$response->hasKey()) {
            throw SpecificationException::generateMustHaveKeyInComponents();
        }

        return new self(
            $this->schemas,
            $this->securitySchemes,
            $this->responses->addResponse($response)
        );
    }

    public function toOpenApiSpecification(): array
    {
        $specifications = [];
        if ($this->responses->isDefined()) {
            $specifications['responses'] =  $this->responses->toOpenApiSpecificationForComponents();
        }
        if ($this->schemas->hasValues()) {
            $specifications['schemas'] =  $this->schemas->toOpenApiSpecification(true);
        }
        if ($this->securitySchemes->isDefined()) {
            $specifications['securitySchemes'] =  $this->securitySchemes->toOpenApiSpecification();
        }
        return $specifications;
    }

    public function isDefined(): bool
    {
        return (
            $this->schemas->hasValues() ||
            $this->securitySchemes->isDefined()
        );
    }
}