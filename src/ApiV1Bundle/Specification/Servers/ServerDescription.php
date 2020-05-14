<?php declare(strict=1);

namespace App\ApiV1Bundle\Specification\Servers;

/**
 * An optional string describing the host designated by the URL.
 * https://swagger.io/specification/#server-object
 */

final class ServerDescription
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