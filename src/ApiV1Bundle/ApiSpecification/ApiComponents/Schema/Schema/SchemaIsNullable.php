<?php declare(strict=1);

namespace App\ApiV1Bundle\ApiSpecification\ApiComponents\Schema\Schema;

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