<?php declare(strict=1);

namespace App\ApiV1Bundle\ApiSpecification;

use App\ApiV1Bundle\ApiSpecification\ApiSecurityRequirement\SecurityRequirementName;
use App\ApiV1Bundle\ApiSpecification\ApiSecurityRequirement\SecurityRequirementScopes;

/**
 * Each name MUST correspond to a security scheme which is declared in the Security Schemes under the Components Object.
 * If the security scheme is of type "oauth2" or "openIdConnect", then the value is a list of scope names required for
 * the execution, and the list MAY be empty if authorization does not require a specified scope.
 * For other security scheme types, the array MUST be empty.
 * @package App\ApiV1Bundle\ApiSpecification\ApiSecurityRequirement
 */

final class ApiSecurityRequirement
{
    private SecurityRequirementName $name;
    private ?SecurityRequirementScopes $scopes;

    private function __construct(SecurityRequirementName $name, ?SecurityRequirementScopes $scopes = null)
    {
        $this->name = $name;
        $this->scopes = $scopes;
    }

    public static function generateOath2(string $name, array $scopes): self
    {
        return new self(SecurityRequirementName::fromString($name), SecurityRequirementScopes::fromArray($scopes));
    }

    public function getName(): SecurityRequirementName
    {
        return $this->name;
    }

    public static function generateOpenIdConnect(array $scopes): self
    {
        return new self(SecurityRequirementName::fromString('openIdConnect'), SecurityRequirementScopes::fromArray($scopes));
    }

    public static function generate(string $name): self
    {
        return new self(SecurityRequirementName::fromString($name));
    }

    public function toOpenApiSpecification(): array
    {
        if ($this->scopes) {
            return [
                $this->name->toString() => $this->scopes->toArray()
            ];
        }

        return [
            $this->name->toString() => []
        ];
    }
}