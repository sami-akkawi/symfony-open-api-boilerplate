<?php declare(strict_types=1);

namespace App\OpenApiSpecification\ApiInfo;

use App\OpenApiSpecification\ApiException\SpecificationException;

/**
 * REQUIRED. The title of the API.
 * http://spec.openapis.org/oas/v3.0.3#info-object
 */

final class InfoTitle
{
    private string $title;

    private function __construct(string $title)
    {
        if (empty($title)) {
            throw SpecificationException::generateEmptyStringException(self::class);
        }
        $this->title = $title;
    }

    public static function fromString(string $title): self
    {
        return new self($title);
    }

    public function toString(): string
    {
        return $this->title;
    }
}