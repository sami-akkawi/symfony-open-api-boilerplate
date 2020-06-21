<?php declare(strict_types=1);

namespace App\OpenApiSpecification\ApiComponents\Response;

use App\OpenApiSpecification\ApiComponents\Response\Response\ResponseDescription;
use App\OpenApiSpecification\ApiComponents\Response\Response\ResponseName;
use App\OpenApiSpecification\ApiComponents\Reference;
use App\OpenApiSpecification\ApiComponents\Response;

/**
 * The Reference Object is defined by JSON Reference and follows the same structure, behavior and rules.
 * http://spec.openapis.org/oas/v3.0.3#reference-object
 */

final class ReferenceResponse extends Response
{
    private Reference $reference;
    private DetailedResponse $response;

    private function __construct(Reference $reference, DetailedResponse $response, ?ResponseName $name = null)
    {
        $this->code = $response->getCode();
        $this->reference = $reference;
        $this->response = $response;
        $this->name = $name;
    }

    public static function generate(string $objectName, DetailedResponse $response): self
    {
        return new self(Reference::generateResponseReference($objectName), $response);
    }

    public function setName(string $name): self
    {
        return new self($this->reference, $this->response,ResponseName::fromString($name));
    }

    public function toOpenApiSpecification(): array
    {
        return $this->reference->toOpenApiSpecification();
    }

    public function toDetailedResponse(): DetailedResponse
    {
        return $this->response;
    }

    public function getDefinedMimeTypes(): array
    {
        return $this->response->getDefinedMimeTypes();
    }

    public function isValueValidByMimeType(string $mimeType, $value): array
    {
        return $this->response->isValueValidByMimeType($mimeType, $value);
    }

    public function getDescription(): ResponseDescription
    {
        return $this->response->getDescription();
    }
}