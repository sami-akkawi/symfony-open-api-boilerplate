<?php declare(strict_types=1);

namespace App\OpenApiSpecification;

use App\OpenApiSpecification\ApiException\SpecificationException;
use App\OpenApiSpecification\ApiPath\PathOperation;
use App\OpenApiSpecification\ApiPath\PathOperation\OperationId;
use App\OpenApiSpecification\ApiPath\PathOperations;
use App\OpenApiSpecification\ApiPath\PathPartialUrl;

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

    public function findOperationById(OperationId $id): ?PathOperation
    {
        return $this->operations->findById($id);
    }

    public function toOpenApiSpecification(): array
    {
        $partialUrl = $this->url->toString();

        $cleanString = substr_replace($partialUrl,"",0, 1);
        $urlParts = explode('/', $cleanString);
        $pathVariables = [];
        foreach ($urlParts as $part) {
            if (strpos($part, '{') === 0 && strpos($part, '}') === strlen($part) - 1) {
                $part = str_replace('{', '', $part);
                $part = str_replace('}', '', $part);
                $pathVariables[] = $part;
            }
        }

        foreach ($this->operations->toArrayOfOperations() as $operation) {
            foreach ($operation->getAllPathParameters() as $parameter) {
                $parameterName = $parameter->getName()->toString();
                if (!in_array($parameterName, $pathVariables)) {
                    throw SpecificationException::generatePathParameterNotDefinedInUrl($parameterName, $operation->getId()->toString());
                }
            }


            foreach ($pathVariables as $variable) {
                $parameter = $operation->getPathParameter($variable);
                if (!$parameter) {
                    throw SpecificationException::generatePathParameterNotDefinedAsSuch($variable, $operation->getId()->toString());
                }
            }
        }

        return [
            $partialUrl => $this->operations->toOpenApiSpecification()
        ];
    }

    public function addOperation(PathOperation $operation): self
    {
        return new self($this->url, $this->operations->addOperation($operation));
    }

    public function mergeOperations(PathOperations $newOperations): self
    {
        $operations = $this->operations;
        foreach ($newOperations->toArrayOfOperations() as $operation) {
            $operations = $operations->addOperation($operation);
        }

        return new self($this->url, $operations);
    }
}