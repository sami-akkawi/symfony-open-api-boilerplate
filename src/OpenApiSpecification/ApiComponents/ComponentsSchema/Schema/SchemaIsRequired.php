<?php declare(strict_types=1);

namespace App\OpenApiSpecification\ApiComponents\ComponentsSchema\Schema;

final class SchemaIsRequired
{
    private bool $isRequired;

    private function __construct(bool $isRequired)
    {
        $this->isRequired = $isRequired;
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
        return $this->isRequired;
    }
}