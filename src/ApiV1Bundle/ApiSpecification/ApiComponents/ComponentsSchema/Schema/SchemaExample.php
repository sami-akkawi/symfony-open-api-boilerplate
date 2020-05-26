<?php declare(strict=1);

namespace App\ApiV1Bundle\ApiSpecification\ApiComponents\ComponentsSchema\Schema;

final class SchemaExample
{
    private string $example;

    private function __construct(string $example)
    {
        $this->example = $example;
    }

    public static function fromString(string $example): self
    {
        return new self($example);
    }

    public function toString(): string
    {
        return $this->example;
    }
}