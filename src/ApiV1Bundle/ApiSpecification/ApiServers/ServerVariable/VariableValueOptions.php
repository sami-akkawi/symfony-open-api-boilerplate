<?php declare(strict=1);

namespace App\ApiV1Bundle\ApiSpecification\ApiServers\ServerVariable;

use App\ApiV1Bundle\ApiSpecification\ApiException\SpecificationException;

/**
 * An enumeration of string values to be used if the substitution options are from a limited set. The array SHOULD
 * NOT be empty.
 * http://spec.openapis.org/oas/v3.0.3#server-variable-object
 */

final class VariableValueOptions
{
    private array $options;

    private function __construct(array $options)
    {
        if (empty($options)) {
            throw SpecificationException::generateEmptyEnumException();
        }
        $this->options = $options;
    }

    public static function fromArray(array $array): self
    {
        return new self($array);
    }

    public function toArray(): array
    {
        return $this->options;
    }
}