<?php declare(strict_types=1);

namespace App\OpenApiSpecification\ApiComponents\ComponentsLink\Link;

use App\OpenApiSpecification\ApiException\SpecificationException;

final class LinkKey
{
    private string $key;

    private function __construct(string $key)
    {
        if (empty(trim($key))) {
            throw SpecificationException::generateEmptyStringException(self::class);
        }
        $this->key = $key;
    }

    public static function fromString(string $key): self
    {
        return new self($key);
    }

    public function toString(): string
    {
        return $this->key;
    }

    public function isIdenticalTo(self $key): bool
    {
        return $this->toString() === $key->toString();
    }
}