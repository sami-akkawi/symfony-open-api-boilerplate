<?php declare(strict=1);

namespace App\ApiV1Bundle\ApiSpecification\ApiComponents\Response\Response;

final class ResponseKey
{
    private string $key;

    private function __construct(string $key)
    {
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