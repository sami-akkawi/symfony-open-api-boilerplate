<?php declare(strict=1);

namespace App\ApiV1Bundle\ApiSpecification;

use App\ApiV1Bundle\ApiSpecification\ApiPath\PathOperation;
use App\ApiV1Bundle\ApiSpecification\ApiPath\PathOperations;
use App\ApiV1Bundle\ApiSpecification\ApiPath\PathPartialUrl;

/**
 * Holds the relative paths to the individual endpoints and their operations. The path is appended to the URL from the
 * Server Object in order to construct the full URL
 * http://spec.openapis.org/oas/v3.0.3#path-item-object
 */

final class ApiPath
{
    private PathPartialUrl $url;
    private PathOperations $operations;

    private function __construct(PathPartialUrl $url, PathOperations $operations)
    {
        $this->url = $url;
        $this->operations = $operations;
    }

    public static function generate(PathPartialUrl $url): self
    {
        return new self($url, PathOperations::generate());
    }

    public function getUrl(): PathPartialUrl
    {
        return $this->url;
    }

    public function getOperations(): PathOperations
    {
        return $this->operations;
    }

    public function toOpenApiSpecification(): array
    {
        return [
            $this->url->toString() => $this->operations->toOpenApiSpecification()
        ];
    }

    public function addOperation(PathOperation $operation): self
    {
        return new self($this->url, $this->operations->addOperation($operation));
    }

    public function mergeOperations(PathOperations $newOperations): self
    {
        $operations = $this->operations;
        foreach ($newOperations->toArray() as $operation) {
            $operations = $operations->addOperation($operation);
        }

        return new self($this->url, $operations);
    }
}