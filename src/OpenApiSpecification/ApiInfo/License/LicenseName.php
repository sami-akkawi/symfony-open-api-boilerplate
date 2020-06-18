<?php declare(strict=1);

namespace App\OpenApiSpecification\ApiInfo\License;

use App\OpenApiSpecification\ApiException\SpecificationException;

/**
 * REQUIRED. The license name used for the API.
 * http://spec.openapis.org/oas/v3.0.3#license-object
 */

final class LicenseName
{
    private string $name;

    private function __construct(string $name)
    {
        if (empty($name)) {
            throw SpecificationException::generateEmptyStringException(self::class);
        }
        $this->name = $name;
    }

    public static function fromString(string $name): self
    {
        return new self($name);
    }

    public function toString(): string
    {
        return $this->name;
    }
}