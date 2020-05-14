<?php declare(strict=1);

namespace App\ApiV1Bundle\Specification\Info\License;

use App\ApiV1Bundle\Specification\Exception\SpecificException;

final class LicenseName
{
    private string $name;

    private function __construct(string $name)
    {
        if (empty($name)) {
            throw SpecificException::generateEmptyStringException(self::class);
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