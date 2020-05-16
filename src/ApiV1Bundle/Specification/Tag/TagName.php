<?php declare(strict=1);

namespace App\ApiV1Bundle\Specification\Tag;

use App\ApiV1Bundle\Specification\Exception\SpecificationException;

/**
 * REQUIRED. The name of the tag.
 * https://swagger.io/specification/#tag-object
 */

final class TagName
{
    private string $name;

    private function __construct(string $name)
    {
        if (empty($name)) {
            throw SpecificationException::generateEmptyStringException(self::class);
        }
        if (!$this->isTagValid($name)) {
            throw SpecificationException::generateTagNotValidException($name);
        }
        $this->name = $name;
    }

    private function isTagValid(string $name): bool
    {
        $capitalCamelCase = str_replace(' ', '', $name);
        // todo: scan folders in the Endpoints folder to check if this exists.

        return true;
    }

    public static function fromString(string $name): self
    {
        return new self($name);
    }

    public function toString(): string
    {
        return $this->name;
    }

    public function isIdenticalTo(self $name): bool
    {
        return $this->toString() === $name->toString();
    }
}