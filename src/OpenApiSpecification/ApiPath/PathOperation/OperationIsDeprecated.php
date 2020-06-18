<?php declare(strict_types=1);

namespace App\OpenApiSpecification\ApiPath\PathOperation;

/**
 * Declares this operation to be deprecated. Consumers SHOULD refrain from usage of the declared operation.
 * Default value is false.
 * http://spec.openapis.org/oas/v3.0.3#operation-object
 */

final class OperationIsDeprecated
{
    private bool $isDeprecated;

    private function __construct(bool $isDeprecated = false)
    {
        $this->isDeprecated = $isDeprecated;
    }

    public static function generate(): self
    {
        return new self();
    }

    public function setTrue(): self
    {
        return new self(true);
    }

    public function setFalse(): self
    {
        return new self(false);
    }

    public function toBool(): bool
    {
        return $this->isDeprecated;
    }
}