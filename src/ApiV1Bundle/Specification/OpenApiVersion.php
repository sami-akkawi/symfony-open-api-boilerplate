<?php declare(strict=1);

namespace App\ApiV1Bundle\Specification;

final class OpenApiVersion
{
    private const VERSION = '3.0.3';
    private string $version;

    private function __construct(string $version)
    {
        $this->version = $version;
    }

    public static function generate(): self
    {
        return new self(self::VERSION);
    }

    public function toString(): string
    {
        return $this->version;
    }
}