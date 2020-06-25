<?php declare(strict_types=1);

namespace App\OpenApiSpecification;

use App\OpenApiSpecification\ApiComponents\ComponentsExample;
use App\OpenApiSpecification\ApiComponents\ComponentsExamples;
use App\OpenApiSpecification\ApiComponents\ComponentsHeader;
use App\OpenApiSpecification\ApiComponents\ComponentsHeaders;
use App\OpenApiSpecification\ApiComponents\Parameter;
use App\OpenApiSpecification\ApiComponents\Parameters;
use App\OpenApiSpecification\ApiComponents\RequestBodies;
use App\OpenApiSpecification\ApiComponents\RequestBody;
use App\OpenApiSpecification\ApiComponents\Response;
use App\OpenApiSpecification\ApiComponents\Responses;
use App\OpenApiSpecification\ApiComponents\Schema;
use App\OpenApiSpecification\ApiComponents\Schemas;
use App\OpenApiSpecification\ApiComponents\SecurityScheme;
use App\OpenApiSpecification\ApiComponents\SecuritySchemes;
use App\OpenApiSpecification\ApiException\SpecificationException;

final class ApiComponents
{
    private Schemas $schemas;
    private Responses $responses;
    private Parameters $parameters;
    private ComponentsExamples $examples;
    private RequestBodies $requestBodies;
    private ComponentsHeaders $headers;
    private SecuritySchemes $securitySchemes;
    // todo: private Links $links

    private function __construct(
        Schemas $schemas,
        Responses $responses,
        Parameters $parameters,
        ComponentsExamples $examples,
        RequestBodies $requestBodies,
        ComponentsHeaders $headers,
        SecuritySchemes $securitySchemes
    ) {
        $this->schemas = $schemas;
        $this->responses = $responses;
        $this->parameters = $parameters;
        $this->examples = $examples;
        $this->requestBodies = $requestBodies;
        $this->headers = $headers;
        $this->securitySchemes = $securitySchemes;
    }

    public static function generate(): self
    {
        return new self(
            Schemas::generate(),
            Responses::generate(),
            Parameters::generate(),
            ComponentsExamples::generate(),
            RequestBodies::generate(),
            ComponentsHeaders::generate(),
            SecuritySchemes::generate()
        );
    }

    public function addSecurityScheme(SecurityScheme $scheme): self
    {
        return new self(
            $this->schemas,
            $this->responses,
            $this->parameters,
            $this->examples,
            $this->requestBodies,
            $this->headers,
            $this->securitySchemes->addScheme($scheme),
        );
    }

    public function addSchema(Schema $schema): self
    {
        if (!$schema->hasName()) {
            throw SpecificationException::generateMustHaveKeyInComponents();
        }

        return new self(
            $this->schemas->addSchema($schema),
            $this->responses,
            $this->parameters,
            $this->examples,
            $this->requestBodies,
            $this->headers,
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
            $this->responses->addResponseToComponents($response),
            $this->parameters,
            $this->examples,
            $this->requestBodies,
            $this->headers,
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
            $this->examples,
            $this->requestBodies,
            $this->headers,
            $this->securitySchemes
        );
    }

    public function addExample(ComponentsExample $example): self
    {
        if (!$example->hasName()) {
            throw SpecificationException::generateMustHaveKeyInComponents();
        }

        return new self(
            $this->schemas,
            $this->responses,
            $this->parameters,
            $this->examples->addExample($example, $example->getName()->toString()),
            $this->requestBodies,
            $this->headers,
            $this->securitySchemes
        );
    }

    public function addRequestBody(RequestBody $requestBody): self
    {
        if (!$requestBody->hasName()) {
            throw SpecificationException::generateMustHaveKeyInComponents();
        }

        return new self(
            $this->schemas,
            $this->responses,
            $this->parameters,
            $this->examples,
            $this->requestBodies->addRequestBody($requestBody),
            $this->headers,
            $this->securitySchemes
        );
    }

    public function addHeader(ComponentsHeader $header): self
    {
        if (!$header->hasDocName()) {
            throw SpecificationException::generateMustHaveKeyInComponents();
        }

        return new self(
            $this->schemas,
            $this->responses,
            $this->parameters,
            $this->examples,
            $this->requestBodies,
            $this->headers->addHeader($header),
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
        if ($this->examples->isDefined()) {
            $specifications['examples'] =  $this->examples->toOpenApiSpecification();
        }
        if ($this->requestBodies->isDefined()) {
            $specifications['requestBodies'] =  $this->requestBodies->toOpenApiSpecification();
        }
        if ($this->headers->isDefined()) {
            $specifications['headers'] =  $this->headers->toOpenApiSpecification();
        }
        if ($this->securitySchemes->isDefined()) {
            $specifications['securitySchemes'] =  $this->securitySchemes->toOpenApiSpecification();
        }
        return $specifications;
    }

    public function isDefined(): bool
    {
        return (
            $this->schemas->isDefined()
            || $this->securitySchemes->isDefined()
            || $this->responses->isDefined()
            || $this->examples->isDefined()
            || $this->requestBodies->isDefined()
            || $this->parameters->isDefined()
        );
    }
}