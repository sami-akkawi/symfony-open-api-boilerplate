<?php declare(strict=1);

namespace App\ApiV1Bundle\ApiSpecification\ApiComponents\ComponentsResponse;

use App\ApiV1Bundle\ApiSpecification\ApiComponents\ComponentsResponse\Response\ResponseHttpCode;
use App\ApiV1Bundle\ApiSpecification\ApiComponents\Reference;
use App\ApiV1Bundle\ApiSpecification\ApiComponents\Response;

/**
 * The Reference Object is defined by JSON Reference and follows the same structure, behavior and rules.
 * http://spec.openapis.org/oas/v3.0.3#reference-object
 */

final class ReferenceResponse extends Response
{
    private Reference $reference;

    private function __construct(ResponseHttpCode $code, Reference $reference)
    {
        $this->code = $code;
    }

    public static function generate(int $httpCode, string $objectName): self
    {
        return new self(ResponseHttpCode::fromInt($httpCode), Reference::generateSchemaReference($objectName));
    }

    public function toOpenApiSpecification(): array
    {
        return $this->reference->toOpenApiSpecification();
    }
}