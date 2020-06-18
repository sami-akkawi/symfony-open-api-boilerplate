<?php declare(strict_types=1);

namespace App\OpenApiSpecification\ApiComponents\Schema\Schema;

final class SchemaItemsAreUnique
{
    private bool $areUnique;

    private function __construct(bool $areUnique)
    {
        $this->areUnique = $areUnique;
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
        return $this->areUnique;
    }
}