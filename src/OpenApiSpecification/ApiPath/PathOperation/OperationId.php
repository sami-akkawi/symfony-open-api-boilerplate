<?php declare(strict_types=1);

namespace App\OpenApiSpecification\ApiPath\PathOperation;

/**
 * Unique string used to identify the operation. The id MUST be unique among all operations described in the API.
 * The operationId value is case-sensitive. Tools and libraries MAY use the operationId to uniquely identify an
 * operation, therefore, it is RECOMMENDED to follow common programming naming conventions.
 * http://spec.openapis.org/oas/v3.0.3#operation-object
 */

final class OperationId
{
    private string $id;

    private function __construct(string $id)
    {
        $this->id = $id;
    }

    public function toString(): string
    {
        return $this->id;
    }

    public static function fromString(string $id): self
    {
        return new self($id);
    }

    public function isIdenticalTo(self $id): bool
    {
        return $this->toString() === $id->toString();
    }
}