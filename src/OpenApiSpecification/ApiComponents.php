<?php declare(strict_types=1);

namespace App\OpenApiSpecification;

use App\OpenApiSpecification\ApiComponents\ComponentsExample;
use App\OpenApiSpecification\ApiComponents\ComponentsExamples;
use App\OpenApiSpecification\ApiComponents\ComponentsHeader;
use App\OpenApiSpecification\ApiComponents\ComponentsHeaders;
use App\OpenApiSpecification\ApiComponents\ComponentsLink;
use App\OpenApiSpecification\ApiComponents\ComponentsLinks;
use App\OpenApiSpecification\ApiComponents\ComponentsParameter;
use App\OpenApiSpecification\ApiComponents\ComponentsParameters;
use App\OpenApiSpecification\ApiComponents\ComponentsRequestBodies;
use App\OpenApiSpecification\ApiComponents\ComponentsRequestBody;
use App\OpenApiSpecification\ApiComponents\ComponentsResponse;
use App\OpenApiSpecification\ApiComponents\ComponentsResponses;
use App\OpenApiSpecification\ApiComponents\ComponentsSchema;
use App\OpenApiSpecification\ApiComponents\ComponentsSchemas;
use App\OpenApiSpecification\ApiComponents\ComponentsSecurityScheme;
use App\OpenApiSpecification\ApiComponents\ComponentsSecuritySchemes;
use App\OpenApiSpecification\ApiException\SpecificationException;

final class ApiComponents
{
    private ComponentsSchemas $schemas;
    private ComponentsResponses $responses;
    private ComponentsParameters $parameters;
    private ComponentsExamples $examples;
    private ComponentsRequestBodies $requestBodies;
    private ComponentsHeaders $headers;
    private ComponentsSecuritySchemes $securitySchemes;
    private ComponentsLinks $links;

    private function __construct(
        ComponentsSchemas $schemas,
        ComponentsResponses $responses,
        ComponentsParameters $parameters,
        ComponentsExamples $examples,
        ComponentsRequestBodies $requestBodies,
        ComponentsHeaders $headers,
        ComponentsSecuritySchemes $securitySchemes,
        ComponentsLinks $links
    ) {
        $this->schemas = $schemas;
        $this->responses = $responses;
        $this->parameters = $parameters;
        $this->examples = $examples;
        $this->requestBodies = $requestBodies;
        $this->headers = $headers;
        $this->securitySchemes = $securitySchemes;
        $this->links = $links;
    }

    public static function generate(): self
    {
        return new self(
            ComponentsSchemas::generate(),
            ComponentsResponses::generate(),
            ComponentsParameters::generate(),
            ComponentsExamples::generate(),
            ComponentsRequestBodies::generate(),
            ComponentsHeaders::generate(),
            ComponentsSecuritySchemes::generate(),
            ComponentsLinks::generate()
        );
    }

    public function addSecurityScheme(ComponentsSecurityScheme $scheme): self
    {
        return new self(
            $this->schemas,
            $this->responses,
            $this->parameters,
            $this->examples,
            $this->requestBodies,
            $this->headers,
            $this->securitySchemes->addScheme($scheme),
            $this->links
        );
    }

    public function addSchema(ComponentsSchema $schema): self
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
            $this->securitySchemes,
            $this->links
        );
    }

    public function addResponse(ComponentsResponse $response): self
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
            $this->securitySchemes,
            $this->links
        );
    }

    public function addParameter(ComponentsParameter $parameter): self
    {
        if (!$parameter->hasKey()) {
            throw SpecificationException::generateMustHaveKeyInComponents();
        }

        return new self(
            $this->schemas,
            $this->responses,
            $this->parameters->addParameter($parameter),
            $this->examples,
            $this->requestBodies,
            $this->headers,
            $this->securitySchemes,
            $this->links
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
            $this->securitySchemes,
            $this->links
        );
    }

    public function addRequestBody(ComponentsRequestBody $requestBody): self
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
            $this->securitySchemes,
            $this->links
        );
    }

    public function addHeader(ComponentsHeader $header): self
    {
        if (!$header->hasKey()) {
            throw SpecificationException::generateMustHaveKeyInComponents();
        }

        return new self(
            $this->schemas,
            $this->responses,
            $this->parameters,
            $this->examples,
            $this->requestBodies,
            $this->headers->addHeader($header),
            $this->securitySchemes,
            $this->links
        );
    }

    public function addLink(ComponentsLink $link): self
    {
        return new self(
            $this->schemas,
            $this->responses,
            $this->parameters,
            $this->examples,
            $this->requestBodies,
            $this->headers,
            $this->securitySchemes,
            $this->links->addLink($link)
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
        if ($this->links->isDefined()) {
            $specifications['links'] =  $this->links->toOpenApiSpecificationForComponents();
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
            || $this->headers->isDefined()
            || $this->parameters->isDefined()
            || $this->links->isDefined()
        );
    }
}