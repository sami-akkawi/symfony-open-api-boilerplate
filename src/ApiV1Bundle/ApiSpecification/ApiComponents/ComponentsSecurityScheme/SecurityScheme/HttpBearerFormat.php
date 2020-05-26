<?php declare(strict=1);
// Created by sami-akkawi on 10.05.20

namespace App\ApiV1Bundle\ApiSpecification\ApiComponents\ComponentsSecurityScheme\SecurityScheme;

final class HttpBearerFormat
{
    private string $format;

    private function __construct(string $format)
    {
        $this->format = $format;
    }

    public static function fromString(string $format): self
    {
        return new self($format);
    }

    public function toString(): string
    {
        return $this->format;
    }
}