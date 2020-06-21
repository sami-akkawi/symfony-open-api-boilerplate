<?php declare(strict_types=1);

namespace App\OpenApiSpecification\ApiComponents\RequestBody;

use App\OpenApiSpecification\ApiComponents\Reference;

final class ReferenceRequestBody
{
    private Reference $reference;
    private DetailedRequestBody $requestBody;

    private function __construct(
        Reference $reference,
        DetailedRequestBody $requestBody,
        ?RequestBodyName $name = null
    ) {
        $this->reference = $reference;
        $this->requestBody = $requestBody;
        $this->name = $name;
    }

    public static function generate(string $objectName, DetailedRequestBody $requestBody): self
    {
        return new self(Reference::generateRequestBodyReference($objectName), $requestBody);
    }

    public function getDefinedMimeTypes(): array
    {
        return $this->requestBody->getDefinedMimeTypes();
    }

    public function isValueValidByMimeType(string $mimeType, $value): array
    {
        return $this->requestBody->isValueValidByMimeType($mimeType, $value);
    }

    public function setName(string $name): self
    {
        return new self($this->reference, $this->requestBody, RequestBodyName::fromString($name));
    }

    public function toDetailedRequestBody(): DetailedRequestBody
    {
        return $this->requestBody;
    }

    public function toOpenApiSpecification(): array
    {
        return $this->reference->toOpenApiSpecification();
    }
}