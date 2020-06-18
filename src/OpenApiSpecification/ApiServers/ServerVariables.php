<?php declare(strict=1);

namespace App\OpenApiSpecification\ApiServers;

use App\OpenApiSpecification\ApiException\SpecificationException;
use App\OpenApiSpecification\ApiServers\ServerVariable\VariableName;

/**
 * A map between a variable name and its value. The value is used for substitution in the server's URL template.
 * http://spec.openapis.org/oas/v3.0.3#server-object
 */

final class ServerVariables
{
    /** @var ServerVariable[] */
    private array $variables;

    private function __construct(array $variables)
    {
        $this->variables = $variables;
    }

    public static function generate(): self
    {
        return new self([]);
    }

    private function hasVariable(VariableName $name): bool
    {
        foreach ($this->variables as $variable) {
            if ($variable->getName()->isIdenticalTo($name)) {
                return true;
            }
        }

        return false;
    }

    public function add(ServerVariable $variable): self
    {
        if ($this->hasVariable($variable->getName())) {
            throw SpecificationException::generateDuplicateDefinitionException($variable->getName()->toString());
        }

        return new self(array_merge($this->variables, [$variable]));
    }

    public function toOpenApiSpecification(): array
    {
        $specifications = [];

        foreach ($this->variables as $variable) {
            $specifications[$variable->getName()->toString()] = $variable->toOpenApiSpecification();
        }

        return $specifications;
    }

    public function hasVariables(): bool
    {
        return (bool)count($this->variables);
    }
}