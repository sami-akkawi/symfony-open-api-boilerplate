<?php declare(strict=1);

namespace App\OpenApiSpecification\ApiPath\PathOperation;

final class OperationHasOptionalSecurity
{
    private bool $hasOptionalSecurity;

    private function __construct(bool $hasOptionalSecurity = false)
    {
        $this->hasOptionalSecurity = $hasOptionalSecurity;
    }

    public static function generate(): self
    {
        return new self();
    }

    public function setTrue(): self
    {
        return new self(true);
    }

    public function setFalse(): self
    {
        return new self(false);
    }

    public function toBool(): bool
    {
        return $this->hasOptionalSecurity;
    }
}