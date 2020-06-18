<?php declare(strict=1);

namespace App\ApiV1Bundle\ApiSpecification\ApiPath\PathOperation;

use App\ApiV1Bundle\ApiSpecification\ApiException\SpecificationException;

final class OperationTags
{
    private array $tags;

    private function __construct(array $tags)
    {
        foreach ($tags as $tag) {
            if (!is_string($tag)) {
                throw SpecificationException::generateInvalidOperationTags();
            }
        }
        $this->tags = $tags;
    }

    public static function generate(): self
    {
        return new self([]);
    }

    public function addTag(string $tag): self
    {
        return new self(array_unique(array_merge($this->tags, [$tag])));
    }

    public function toArray()
    {
        return $this->tags;
    }

    public function isDefined(): bool
    {
        return (bool)count($this->tags);
    }

    public function addTags(self $tags): self
    {
        return new self(array_unique(array_merge($this->toArray(), $tags->toArray())));
    }
}