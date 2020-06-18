<?php declare(strict=1);

namespace App\OpenApiSpecification\ApiComponents\Reference;

use App\OpenApiSpecification\ApiException\SpecificationException;

/**
 * REQUIRED. The reference string.
 * http://spec.openapis.org/oas/v3.0.3#reference-object
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