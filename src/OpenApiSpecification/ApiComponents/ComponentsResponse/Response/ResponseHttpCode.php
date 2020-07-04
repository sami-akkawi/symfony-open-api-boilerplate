<?php declare(strict_types=1);

namespace App\OpenApiSpecification\ApiComponents\ComponentsResponse\Response;

use App\OpenApiSpecification\ApiException\SpecificationException;

/**
 * Any HTTP status code can be used as the property name, but only one property per code, to describe the expected
 * response for that HTTP status code. A Reference Object can link to a response that is defined in the OpenAPI Object's
 * components/responses section. This field MUST be enclosed in quotation marks (for example, "200") for compatibility
 * between JSON and YAML. To define a range of response codes, this field MAY contain the uppercase wildcard character X.
 * For example, 2XX represents all response codes between [200-299]. Only the following range definitions are allowed:
 * 1XX, 2XX, 3XX, 4XX, and 5XX. If a response is defined using an explicit code, the explicit code definition takes
 * precedence over the range definition for that code.
 * http://spec.openapis.org/oas/v3.0.3#responses-object
 */

final class ResponseHttpCode
{
    private const OK                   = '200';
    private const CREATED              = '201';
    private const BAD_REQUEST          = '400';
    private const UNAUTHORIZED         = '401';
    private const FORBIDDEN            = '403';
    private const NOT_FOUND            = '404';
    private const UNPROCESSABLE_ENTITY = '422';
    private const CORRUPT_DATA         = '588';

    private string $statusCode;

    private function __construct(string $statusCode)
    {
        if (empty($statusCode)) {
            throw SpecificationException::generateEmptyStringException(self::class);
        }
        $this->statusCode = $statusCode;
    }

    public static function fromInt(int $statusCode): self
    {
        return new self((string)$statusCode);
    }

    public static function fromString(string $statusCode): self
    {
        return new self($statusCode);
    }

    public static function generateOk(): self
    {
        return new self(self::OK);
    }

    public static function generateCreated(): self
    {
        return new self(self::CREATED);
    }

    public static function generateBadRequest(): self
    {
        return new self(self::BAD_REQUEST);
    }

    public static function generateUnauthorized(): self
    {
        return new self(self::UNAUTHORIZED);
    }

    public static function generateForbidden(): self
    {
        return new self(self::FORBIDDEN);
    }

    public static function generateNotFound(): self
    {
        return new self(self::NOT_FOUND);
    }

    public static function generateUnprocessableEntity(): self
    {
        return new self(self::UNPROCESSABLE_ENTITY);
    }

    public static function generateCorruptData(): self
    {
        return new self(self::CORRUPT_DATA);
    }

    public function toString(): string
    {
        return $this->statusCode;
    }

    public function isIdenticalTo(self $statusCode): bool
    {
        return $this->toString() === $statusCode->toString();
    }
}