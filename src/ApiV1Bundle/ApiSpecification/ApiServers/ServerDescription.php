<?php declare(strict=1);

namespace App\ApiV1Bundle\ApiSpecification\ApiServers;

/**
 * An optional string describing the host designated by the URL.
 * http://spec.openapis.org/oas/v3.0.3#server-object
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