<?php declare(strict_types=1);

namespace App\OpenApiSpecification\ApiComponents\ComponentsRequestBody;

use App\OpenApiSpecification\ApiComponents\ComponentsRequestBody;
use App\OpenApiSpecification\ApiComponents\ComponentsRequestBody\RequestBody\RequestBodyName;
use App\OpenApiSpecification\ApiComponents\Reference;

final class ReferenceRequestBody extends ComponentsRequestBody
{
    private Reference $reference;
    private RequestBody $requestBody;

    private function __construct(
        Reference $reference,
        RequestBody $requestBody,
        ?RequestBodyName $name = null
    ) {
        $this->reference = $reference;
        $this->requestBody = $requestBody;
        $this->name = $name;
    }

    public static function generate(string $objectName, RequestBody $requestBody): self
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

    public function toRequestBody(): RequestBody
    {
        return $this->requestBody;
    }

    public function toOpenApiSpecification(): array
    {
        return $this->reference->toOpenApiSpecification();
    }
}