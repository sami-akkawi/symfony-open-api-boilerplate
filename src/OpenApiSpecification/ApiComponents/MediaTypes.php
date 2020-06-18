<?php declare(strict_types=1);

namespace App\OpenApiSpecification\ApiComponents;

use App\OpenApiSpecification\ApiComponents\MediaType\MediaTypeMimeType;
use App\OpenApiSpecification\ApiException\SpecificationException;

final class MediaTypes
{
    /** @var MediaType[] */
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

    public function addMediaType(MediaType $mediaType): self
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