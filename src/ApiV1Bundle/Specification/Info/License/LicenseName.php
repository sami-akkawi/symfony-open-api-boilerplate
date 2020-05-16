<?php declare(strict=1);

namespace App\ApiV1Bundle\Specification\Info\License;

use App\ApiV1Bundle\Specification\Exception\SpecificationException;

/**
 * REQUIRED. The license name used for the API.
 * https://swagger.io/specification/#license-object
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