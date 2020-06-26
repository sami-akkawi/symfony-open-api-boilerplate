<?php declare(strict_types=1);

namespace App\OpenApiSpecification\ApiComponents\ComponentsSchema\Schema;

final class SchemaIsNullable
{
    private bool $isNullable;

    private function __construct(bool $isNullable)
    {
        $this->isNullable = $isNullable;
    }

    public static function generateTrue(): self
    {
        return new self(true);
    }

    public static function generateFalse(): self
    {
        return new self(false);
    }

    public function toBool(): bool
    {
        return $this->isNullable;
    }
}