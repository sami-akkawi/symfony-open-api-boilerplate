<?php declare(strict_types=1);

namespace App\OpenApiSpecification\ApiComponents\Parameter;

use App\OpenApiSpecification\ApiException\SpecificationException;

/**
 * REQUIRED. The name of the parameter. Parameter names are case sensitive.
 * * If in is "path", the name field MUST correspond to a template expression occurring within the path field in the
 *      Paths Object. See Path Templating for further information.
 * * If in is "header" and the name field is "Accept", "Content-Type" or "Authorization", the parameter definition
 *      SHALL be ignored.
 * * For all other cases, the name corresponds to the parameter name used by the in property.
 * http://spec.openapis.org/oas/v3.0.3#parameter-object
 */

final class ParameterName
{
    private string $name;

    private function __construct(string $name)
    {
        if (empty(trim($name))) {
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