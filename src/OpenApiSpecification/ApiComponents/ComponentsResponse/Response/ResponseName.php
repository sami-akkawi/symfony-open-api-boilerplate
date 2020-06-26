<?php declare(strict_types=1);

namespace App\OpenApiSpecification\ApiComponents\ComponentsResponse\Response;

final class ResponseName
{
    private string $name;

    private function __construct(string $name)
    {
        $this->name = $name;
    }

    public static function fromString(string $name): self
    {
        return new self($name);
    }

    public function toString(): string
    {
        return $this->name;
    }

    public function isIdenticalTo(self $name): bool
    {
        return $this->toString() === $name->toString();
    }
}