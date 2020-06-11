<?php declare(strict=1);

namespace App\ApiV1Bundle\ApiSpecification;

use App\ApiV1Bundle\ApiSpecification\ApiComponents\Parameter;
use App\ApiV1Bundle\ApiSpecification\ApiComponents\Parameters;
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
    private Parameters $parameters;
    // todo: private Examples $examples
    // todo: private RequestBodies $requestBodies
    // todo: private Headers $headers
    private SecuritySchemes $securitySchemes;
    // todo: private Links $links

    private function __construct(
        Schemas $schemas,
        Responses $responses,
        Parameters $parameters,
        SecuritySchemes $securitySchemes
    ) {
        $this->schemas = $schemas;
        $this->responses = $responses;
        $this->parameters = $parameters;
        $this->securitySchemes = $securitySchemes;
    }

    public static function generate(): self
    {
        return new self(
            Schemas::generate(),
            Responses::generate(),
            Parameters::generate(),
            SecuritySchemes::generate()
        );
    }

    public function addSecurityScheme(SecurityScheme $scheme): self
    {
        return new self(
            $this->schemas,
            $this->responses,
            $this->parameters,
            $this->securitySchemes->addScheme($scheme),
        );
    }

    public function addSchema(Schema $schema): self
    {
        return new self(
            $this->schemas->addSchema($schema),
            $this->responses,
            $this->parameters,
            $this->securitySchemes
        );
    }

    public function addResponse(Response $response): self
    {
        if (!$response->hasName()) {
            throw SpecificationException::generateMustHaveKeyInComponents();
        }

        return new self(
            $this->schemas,
            $this->responses->addResponse($response),
            $this->parameters,
            $this->securitySchemes
        );
    }

    public function addParameter(Parameter $parameter): self
    {
        if (!$parameter->hasDocName()) {
            throw SpecificationException::generateMustHaveKeyInComponents();
        }

        return new self(
            $this->schemas,
            $this->responses,
            $this->parameters->addParameter($parameter),
            $this->securitySchemes
        );
    }



    public function toOpenApiSpecification(): array
    {
        $specifications = [];
        if ($this->responses->isDefined()) {
            $specifications['responses'] =  $this->responses->toOpenApiSpecificationForComponents();
        }
        if ($this->schemas->isDefined()) {
            $specifications['schemas'] =  $this->schemas->toOpenApiSpecificationForComponents();
        }
        if ($this->parameters->isDefined()) {
            $specifications['parameters'] =  $this->parameters->toOpenApiSpecificationForComponents();
        }
        if ($this->securitySchemes->isDefined()) {
            $specifications['securitySchemes'] =  $this->securitySchemes->toOpenApiSpecification();
        }
        return $specifications;
    }

    public function isDefined(): bool
    {
        return (
            $this->schemas->isDefined() ||
            $this->securitySchemes->isDefined() ||
            $this->parameters->isDefined() ||
            $this->responses->isDefined()
        );
    }
}