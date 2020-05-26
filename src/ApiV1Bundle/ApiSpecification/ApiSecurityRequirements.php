<?php declare(strict=1);

namespace App\ApiV1Bundle\ApiSpecification;

use App\ApiV1Bundle\ApiSpecification\ApiException\SpecificationException;
use App\ApiV1Bundle\ApiSpecification\ApiSecurityRequirement\SecurityRequirementName;

/**
 * Lists the required security schemes to execute this operation. The name used for each property MUST correspond
 * to a security scheme declared in the Security Schemes under the Components Object.
 * Security Requirement Objects that contain multiple schemes require that all schemes MUST be satisfied
 * for a request to be authorized. This enables support for scenarios where multiple query parameters or HTTP headers
 * are required to convey security information.
 * When a list of Security Requirement Objects is defined on the OpenAPI Object or Operation Object, only one of the
 * Security Requirement Objects in the list needs to be satisfied to authorize the request.
 * http://spec.openapis.org/oas/v3.0.3#security-requirement-object
 */

final class ApiSecurityRequirements
{
    /** @var ApiSecurityRequirement[] */
    private array $securityRequirements;

    private function __construct(array $securityRequirements)
    {
        $this->securityRequirements = $securityRequirements;
    }

    public static function generate(): self
    {
        return new self([]);
    }

    private function hasRequirement(?SecurityRequirementName $name): bool
    {
        foreach ($this->securityRequirements as $requirement) {
            $requirementName = $requirement->getName();
            if (is_null($requirementName) && is_null($name)) {
                return true;
            }
            if (is_null($requirementName) || is_null($name)) {
                continue;
            }
            if ($requirementName->isIdenticalTo($name)) {
                return true;
            }
        }
        return false;
    }

    public function addRequirement(ApiSecurityRequirement $requirement): self
    {
        if ($this->hasRequirement($requirement->getName())) {
            throw SpecificationException::generateDuplicateDefinitionException(
                $requirement->getName() ? $requirement->getName()->toString() : 'insecure'
            );
        }

        return new self(array_merge($this->securityRequirements, [$requirement]));
    }

    public function toOpenApiSpecification(): array
    {
        $specifications = [];

        foreach ($this->securityRequirements as $requirement) {
            $specifications[] = $requirement->toOpenApiSpecification();
        }

        return $specifications;
    }
}