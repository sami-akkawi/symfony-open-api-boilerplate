<?php declare(strict_types=1);

namespace App\OpenApiSpecification\ApiComponents\ComponentsResponse;

use App\OpenApiSpecification\ApiComponents\ComponentsResponse;
use App\OpenApiSpecification\ApiComponents\ComponentsResponse\Response\ResponseDescription;
use App\OpenApiSpecification\ApiComponents\ComponentsResponse\Response\ResponseName;
use App\OpenApiSpecification\ApiComponents\Reference;

/**
 * The Reference Object is defined by JSON Reference and follows the same structure, behavior and rules.
 * http://spec.openapis.org/oas/v3.0.3#reference-object
 */

final class ReferenceResponse extends ComponentsResponse
{
    private Reference $reference;
    private ResponseSchema $response;

    private function __construct(Reference $reference, ResponseSchema $response, ?ResponseName $name = null)
    {
        $this->code = $response->getCode();
        $this->reference = $reference;
        $this->response = $response;
        $this->name = $name;
    }

    public static function generate(string $objectName, ResponseSchema $response): self
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

    public function toResponse(): ResponseSchema
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