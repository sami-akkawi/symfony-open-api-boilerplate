<?php declare(strict=1);

namespace App\ApiV1Bundle\Specification\Servers\ServerVariable;

use App\ApiV1Bundle\Specification\Exception\SpecificException;

/**
 * REQUIRED. The default value to use for substitution, which SHALL be sent if an alternate value is not supplied.
 * Note this behavior is different than the Schema Object's treatment of default values, because in those cases
 * parameter values are optional. If the enum is defined, the value SHOULD exist in the enum's values.
 * https://swagger.io/specification/#server-variable-object
 */

final class VariableDefaultValue
{
    private string $default;

    private function __construct(string $default)
    {
        if (empty($default)) {
            throw SpecificException::generateEmptyStringException(self::class);
        }
        $this->default = $default;
    }

    public static function fromString(string $default): self
    {
        return new self($default);
    }

    public function toString(): string
    {
        return $this->default;
    }
}