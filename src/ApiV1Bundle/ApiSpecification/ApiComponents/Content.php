<?php declare(strict=1);

namespace App\ApiV1Bundle\ApiSpecification\ApiComponents;

use App\ApiV1Bundle\ApiSpecification\ApiComponents\Content\ContentMediaType;
use App\ApiV1Bundle\ApiSpecification\ApiComponents\Content\ContentMediaTypes;
use App\ApiV1Bundle\ApiSpecification\ApiException\SpecificationException;

final class Content
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