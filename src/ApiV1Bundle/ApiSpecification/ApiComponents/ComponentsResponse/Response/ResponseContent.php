<?php declare(strict=1);

namespace App\ApiV1Bundle\ApiSpecification\ApiComponents\ComponentsResponse\Response;

use App\ApiV1Bundle\ApiSpecification\ApiComponents\ComponentsResponse\Response\ResponseContent\ContentMediaType;
use App\ApiV1Bundle\ApiSpecification\ApiComponents\ComponentsResponse\Response\ResponseContent\ContentMediaTypes;
use App\ApiV1Bundle\ApiSpecification\ApiException\SpecificationException;

final class ResponseContent
{
    private ContentMediaTypes $mediaTypes;

    private function __construct(ContentMediaTypes $mediaTypes)
    {
        $this->mediaTypes = $mediaTypes;
    }

    public static function generate(): self
    {
        return new self(ContentMediaTypes::generate());
    }

    public function addMediaType(ContentMediaType $mediaType): self
    {
        return new self($this->mediaTypes->addMediaType($mediaType));
    }

    public function toOpenApi3Specification(): array
    {
        if (!$this->mediaTypes->hasValues()) {
            throw SpecificationException::generateMediaTypesMustBeDefined();
        }
        return $this->mediaTypes->toOpenApiSpecification();
    }
}