<?php declare(strict=1);

namespace App\ApiV1Bundle\ApiSpecification\ApiComponents\Schema\Schema;

/**
 * Specifies that a schema is deprecated and SHOULD be transitioned out of usage. Default value is false.
 * http://spec.openapis.org/oas/v3.0.3#fixed-fields-19
 */

final class SchemaIsDeprecated
{
    private bool $isDeprecated;

    private function __construct(bool $isDeprecated)
    {
        $this->isDeprecated = $isDeprecated;
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
        return $this->isDeprecated;
    }
}