<?php declare(strict=1);

namespace App\ApiV1Bundle\ApiSpecification\ApiComponents\RequestContent;

use App\ApiV1Bundle\ApiSpecification\ApiComponents\Parameters;
use App\ApiV1Bundle\ApiSpecification\ApiComponents\ResponseContent\MediaType\MediaTypeMimeType;

final class ContentMediaType
{
    private MediaTypeMimeType $mimeType;
    private Parameters $parameters;

    private function __construct(MediaTypeMimeType $mimeType, Parameters $parameter)
    {
        $this->mimeType = $mimeType;
        $this->parameters = $parameter;
    }

    public static function generateJson(Parameters $parameters): self
    {
        return new self(MediaTypeMimeType::generateJson(), $parameters);
    }

    public function getMimeType(): MediaTypeMimeType
    {
        return $this->mimeType;
    }

    public function toOpenApiSpecification(): array
    {
        return ['schema' => $this->parameters->toOpenApiSpecificationForComponents()];
    }
}