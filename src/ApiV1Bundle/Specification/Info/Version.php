<?php declare(strict=1);

namespace App\ApiV1Bundle\Specification\Info;

/**
 * REQUIRED. The version of the OpenAPI document (which is distinct from the OpenAPI Specification
 * version or the API implementation version).
 * https://swagger.io/specification/#info-object
 */

final class Version
{
    protected const MAJOR_VERSION = 1;
    protected const MINOR_VERSION = 0;
    protected const PATCH_VERSION = 0;

    public static function getVersion(): string
    {
        return self::MAJOR_VERSION . "." . self::MINOR_VERSION . "." . self::PATCH_VERSION;
    }

    public static function getSubVersion(): string
    {
        return self::MAJOR_VERSION . "." . self::MINOR_VERSION;
    }

    public static function getMajorVersion(): int
    {
        return self::MAJOR_VERSION;
    }

    public static function getMinorVersion(): int
    {
        return self::MINOR_VERSION;
    }

    public static function getPatchLevel(): int
    {
        return self::PATCH_VERSION;
    }
}