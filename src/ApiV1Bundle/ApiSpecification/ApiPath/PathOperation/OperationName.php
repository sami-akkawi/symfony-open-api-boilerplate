<?php declare(strict=1);

namespace App\ApiV1Bundle\ApiSpecification\ApiPath\PathOperation;

use App\ApiV1Bundle\ApiSpecification\ApiException\SpecificationException;

/**
 * Describes a single API operation on a path.
 * http://spec.openapis.org/oas/v3.0.3#operation-object
 */

final class OperationName
{
    private const GET     = 'get';
    private const POST    = 'post';
    private const PUT     = 'put';
    private const PATCH   = 'patch';
    private const DELETE  = 'delete';
    private const OPTIONS = 'options';

    private const VALID_OPERATIONS = [
        self::GET,
        self::POST,
        self::PUT,
        self::PATCH,
        self::DELETE,
        self::OPTIONS
    ];

    private string $name;

    private function __construct(string $name)
    {
        if (!in_array($name, self::VALID_OPERATIONS)) {
            throw SpecificationException::generateInvalidOperationName($name);
        }
        $this->name = $name;
    }

    public function toString(): string
    {
        return $this->name;
    }

    public function isIdenticalTo(self $name): bool
    {
        return $this->toString() === $name->toString();
    }

    public static function getValidOperations(): array
    {
        return self::VALID_OPERATIONS;
    }

    public static function generateGet(): self
    {
        return new self(self::GET);
    }

    public static function generatePost(): self
    {
        return new self(self::POST);
    }

    public static function generatePut(): self
    {
        return new self(self::PUT);
    }

    public static function generatePatch(): self
    {
        return new self(self::PATCH);
    }

    public static function generateDelete(): self
    {
        return new self(self::DELETE);
    }

    public static function generateOptions(): self
    {
        return new self(self::OPTIONS);
    }
}