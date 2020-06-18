<?php declare(strict_types=1);

namespace App\OpenApiSpecification\ApiSecurityRequirement;

use App\OpenApiSpecification\ApiException\SpecificationException;

/**
 * Each name MUST correspond to a security scheme which is declared in the Security Schemes under the Components Object.
 * If the security scheme is of type "oauth2" or "openIdConnect", then the value is a list of scope names required for
 * the execution, and the list MAY be empty if authorization does not require a specified scope.
 * For other security scheme types, the array MUST be empty.
 * @package App\OpenApiSpecification\ApiSecurityRequirement
 */

final class SecurityRequirementScopes
{
    private array $scopes;

    private function __construct(array $scopes)
    {
        if (count(array_unique($scopes)) !== count($scopes)) {
            throw SpecificationException::generateDuplicateSecurityRequirementsException();
        }
        $this->scopes = $scopes;
    }

    public static function fromArray(array $array): self
    {
        return new self($array);
    }

    public function toArray(): array
    {
        return $this->scopes;
    }
}