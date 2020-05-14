<?php declare(strict=1);

namespace App\ApiV1Bundle\Specification\Info;

use App\ApiV1Bundle\Specification\Exception\SpecificException;

/**
 * REQUIRED. The title of the API.
 * https://swagger.io/specification/#info-object
 */

final class Title
{
    private string $title;

    private function __construct(string $title)
    {
        if (empty($title)) {
            throw SpecificException::generateEmptyStringException(self::class);
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