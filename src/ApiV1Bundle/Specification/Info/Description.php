<?php declare(strict=1);

namespace App\ApiV1Bundle\Specification\Info;

/**
 * A short description of the API.
 * https://swagger.io/specification/#info-object
 */

final class Description
{
    private string $description;

    private function __construct(string $description)
    {
        $this->description = $description;
    }

    public static function fromString(string $description): self
    {
        return new self($description);
    }

    public function toString(): string
    {
        return $this->description;
    }
}