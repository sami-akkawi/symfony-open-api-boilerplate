<?php declare(strict=1);

namespace App\ApiV1Bundle\Specification\SecurityRequirement;

use App\ApiV1Bundle\Specification\Exception\SpecificationException;

/**
 * Each name MUST correspond to a security scheme which is declared in the Security Schemes under the Components Object.
 * If the security scheme is of type "oauth2" or "openIdConnect", then the value is a list of scope names required for
 * the execution, and the list MAY be empty if authorization does not require a specified scope.
 * For other security scheme types, the array MUST be empty.
 * @package Easycharter\ApiV3Bundle\ApiSpecification\ApiSecurityRequirement
 */

final class SecurityRequirementName
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

    public function isIdenticalTo(self $name): bool
    {
        return $this->toString() === $name->toString();
    }
}