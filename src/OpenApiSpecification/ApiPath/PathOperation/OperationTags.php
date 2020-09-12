<?php declare(strict_types=1);

namespace App\OpenApiSpecification\ApiPath\PathOperation;

use App\OpenApiSpecification\ApiTag;
use App\OpenApiSpecification\ApiTags;

final class OperationTags
{
    private ApiTags $tags;

    private function __construct(ApiTags $tags)
    {
        $this->tags = $tags;
    }

    public static function generate(): self
    {
        return new self(ApiTags::generate());
    }

    public function addTag(ApiTag $tag): self
    {
        return new self($this->tags->addTag($tag));
    }

    public function toApiTags(): ApiTags
    {
        return $this->tags;
    }

    public function toOpenApiSpecification(): array
    {
        $names = [];
        foreach ($this->tags->toTags() as $tag) {
            $names[] = $tag->getName()->toString();
        }
        return $names;
    }

    public function isDefined(): bool
    {
        return $this->tags->hasTags();
    }

    public function addTags(self $tags): self
    {
        return new self($this->tags->mergeTags($tags->toApiTags()));
    }
}