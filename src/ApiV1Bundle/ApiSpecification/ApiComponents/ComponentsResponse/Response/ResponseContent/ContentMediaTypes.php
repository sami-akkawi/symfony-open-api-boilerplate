<?php declare(strict=1);

namespace App\ApiV1Bundle\ApiSpecification\ApiComponents\ComponentsResponse\Response\ResponseContent;

use App\ApiV1Bundle\ApiSpecification\ApiComponents\ComponentsResponse\Response\ResponseContent\MediaType\MediaTypeMimeType;
use App\ApiV1Bundle\ApiSpecification\ApiException\SpecificationException;

final class ContentMediaTypes
{
    /** @var ContentMediaType[] */
    private array $mediaTypes;

    private function __construct(array $mediaTypes)
    {
        $this->mediaTypes = $mediaTypes;
    }

    public static function generate(): self
    {
        return new self([]);
    }

    private function hasMediaType(MediaTypeMimeType $mimeType): bool
    {
        foreach ($this->mediaTypes as $mediaType) {
            if ($mediaType->getMimeType()->isIdenticalTo($mimeType)) {
                return true;
            }
        }

        return false;
    }

    public function addMediaType(ContentMediaType $mediaType): self
    {
        if ($this->hasMediaType($mediaType->getMimeType())) {
            throw SpecificationException::generateDuplicateDefinitionException($mediaType->getMimeType()->toString());
        }
        return new self(array_merge($this->mediaTypes, [$mediaType]));
    }

    public function toOpenApiSpecification(bool $sorted = false): array
    {
        $mimeTypes = [];
        foreach ($this->mediaTypes as $mediaType) {
            $mimeTypes[$mediaType->getMimeType()->toString()] = $mediaType->toOpenApiSpecification();
        }
        if ($sorted) {
            ksort($mimeTypes);
        }
        return $mimeTypes;
    }

    public function hasValues(): bool
    {
        return (bool)count($this->mediaTypes);
    }
}