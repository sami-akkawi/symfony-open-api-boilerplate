<?php declare(strict=1);

namespace App\ApiV1Bundle\ApiSpecification\ApiComponents\ComponentsReference;

use App\ApiV1Bundle\ApiSpecification\ApiException\SpecificationException;

/**
 * REQUIRED. The reference string.
 * https://swagger.io/specification/#reference-object
 */

final class ReferenceObjectName
{
    private string $objectName;

    private function __construct(string $objectName)
    {
        if (empty($objectName)) {
            throw SpecificationException::generateEmptyStringException(self::class);
        }
        $this->objectName = $objectName;
    }

    public static function fromString(string $objectName): self
    {
        return new self($objectName);
    }

    public function toString(): string
    {
        return $this->objectName;
    }

    public function isIdenticalTo(self $objectName): bool
    {
        return $this->toString() === $objectName->toString();
    }
}