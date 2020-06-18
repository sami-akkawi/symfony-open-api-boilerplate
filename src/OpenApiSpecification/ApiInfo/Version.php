<?php declare(strict=1);

namespace App\OpenApiSpecification\ApiInfo;

/**
 * REQUIRED. The version of the OpenAPI document (which is distinct from the OpenAPI Specification
 * version or the API implementation version).
 * http://spec.openapis.org/oas/v3.0.3#info-object
 */

final class Version
{
    private int $majorVersion;
    private int $minorVersion;
    private int $patchVersion;

    private function __construct(int $majorVersion, int $minorVersion, int $patchVersion)
    {
        $this->majorVersion = $majorVersion;
        $this->minorVersion = $minorVersion;
        $this->patchVersion = $patchVersion;
    }

    public static function generate(int $majorVersion, int $minorVersion, int $patchVersion): self
    {
        return new self($majorVersion, $minorVersion, $patchVersion);
    }

    public function getFullVersion(): string
    {
        return $this->majorVersion . "." . $this->minorVersion . "." . $this->patchVersion;
    }

    public function getSubVersion(): string
    {
        return $this->majorVersion . "." . $this->minorVersion;
    }

    public function getMajorVersion(): int
    {
        return $this->majorVersion;
    }

    public function getMinorVersion(): int
    {
        return $this->minorVersion;
    }

    public function getPatchLevel(): int
    {
        return $this->patchVersion;
    }
}