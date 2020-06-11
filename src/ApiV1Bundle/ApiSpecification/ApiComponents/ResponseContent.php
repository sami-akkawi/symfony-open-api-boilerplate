<?php declare(strict=1);

namespace App\ApiV1Bundle\ApiSpecification\ApiComponents;

use App\ApiV1Bundle\ApiSpecification\ApiComponents\ResponseContent\ContentMediaType;
use App\ApiV1Bundle\ApiSpecification\ApiComponents\ResponseContent\ContentMediaTypes;
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

    public function toOpenApiSpecification(): array
    {
        if (!$this->mediaTypes->hasValues()) {
            throw SpecificationException::generateMediaTypesMustBeDefined();
        }
        return $this->mediaTypes->toOpenApiSpecification();
    }
}