<?php declare(strict_types=1);

namespace App\OpenApiSpecification\ApiComponents\ComponentsLink\Link;

use App\OpenApiSpecification\ApiPath\PathOperation\OperationId;

/**
 * The name of an existing, resolvable OAS operation, as defined with a unique operationId.
 * http://spec.openapis.org/oas/v3.0.3#link-object
 */

final class LinkOperationId
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

    public static function fromOperationId(OperationId $id): self
    {
        return new self($id->toString());
    }
}