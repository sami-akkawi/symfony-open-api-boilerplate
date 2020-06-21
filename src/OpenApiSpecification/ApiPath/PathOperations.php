<?php declare(strict_types=1);

namespace App\OpenApiSpecification\ApiPath;

use App\OpenApiSpecification\ApiException\SpecificationException;
use App\OpenApiSpecification\ApiPath\PathOperation\OperationName;

/**
 * Describes the operations available on a single path. A Path Item MAY be empty, due to ACL constraints.
 * The path itself is still exposed to the documentation viewer but they will not know which operations and parameters
 * are available.
 * http://spec.openapis.org/oas/v3.0.3#path-item-object
 */

final class PathOperations
{
    /** @var PathOperation[] */
    private array $operations;

    private function __construct(array $operations)
    {
        $this->operations = $operations;
    }

    public function toArrayOfOperations(): array
    {
        return $this->operations;
    }

    public static function generate(): self
    {
        return new self([]);
    }

    private function hasOperation(OperationName $name): bool
    {
        foreach ($this->operations as $operation) {
            if ($operation->getName()->isIdenticalTo($name)) {
                return true;
            }
        }

        return false;
    }

    public function addOperation(PathOperation $operation): self
    {
        if ($this->hasOperation($operation->getName())) {
            throw SpecificationException::generatePathOperationAlreadyDefined($operation->getName()->toString());
        }
        return new self(array_merge($this->operations, [$operation]));
    }

    public function toOpenApiSpecification(): array
    {
        $specifications = [];
        foreach ($this->operations as $operation) {
            $specifications[$operation->getName()->toString()] = $operation->toOpenApiSpecification();
        }
        ksort($specifications);
        return $specifications;
    }
}