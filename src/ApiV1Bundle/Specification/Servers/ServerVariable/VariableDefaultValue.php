<?php declare(strict=1);

namespace App\ApiV1Bundle\Specification\Servers\ServerVariable;

use App\ApiV1Bundle\Specification\Exception\SpecificException;

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