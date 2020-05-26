<?php declare(strict=1);
// Created by sami-akkawi on 19.05.20

namespace App\ApiV1Bundle\ApiSpecification\ApiComponents\ComponentsResponse\Response;

/**
 * REQUIRED. A short description of the response. CommonMark syntax MAY be used for rich text representation.
 * https://swagger.io/specification/#response-object
 */

final class ResponseDescription
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