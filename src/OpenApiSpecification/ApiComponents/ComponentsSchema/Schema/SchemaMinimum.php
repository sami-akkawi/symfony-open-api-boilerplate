<?php declare(strict_types=1);

namespace App\OpenApiSpecification\ApiComponents\ComponentsSchema\Schema;

final class SchemaMinimum
{
    private float $minimum;

    private function __construct(float $minimum)
    {
        $this->minimum = $minimum;
    }

    public static function fromInt(int $minimum): self
    {
        return new self((float)$minimum);
    }

    public static function fromFloat(float $minimum): self
    {
        return new self($minimum);
    }

    public function toInt(): int
    {
        return (int)$this->minimum;
    }

    public function toFloat(): float
    {
        return $this->minimum;
    }
}