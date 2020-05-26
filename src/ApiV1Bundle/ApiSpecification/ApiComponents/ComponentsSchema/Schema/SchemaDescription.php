<?php declare(strict=1);
// Created by sami-akkawi on 16.05.20

namespace App\ApiV1Bundle\ApiSpecification\ApiComponents\ComponentsSchema\Schema;

final class SchemaDescription
{
    private string $description;

    private function __construct(string $description)
    {
        $this->description = $description;
    }

    public static function fromString(string $description): self
    {
        return new self($description);
    }

    public function toString(): string
    {
        return $this->description;
    }
}