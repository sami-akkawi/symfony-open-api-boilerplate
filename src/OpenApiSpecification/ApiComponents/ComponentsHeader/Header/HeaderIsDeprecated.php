<?php declare(strict_types=1);

namespace App\OpenApiSpecification\ApiComponents\ComponentsHeader\Header;

/**
 * Specifies that a Header is deprecated and SHOULD be transitioned out of usage. Default value is false.
 * http://spec.openapis.org/oas/v3.0.3#Header-object
 */

final class HeaderIsDeprecated
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