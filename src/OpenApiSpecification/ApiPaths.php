<?php declare(strict_types=1);

namespace App\OpenApiSpecification;

use App\OpenApiSpecification\ApiException\SpecificationException;
use App\OpenApiSpecification\ApiPath\PathOperation;
use App\OpenApiSpecification\ApiPath\PathOperation\OperationId;
use App\OpenApiSpecification\ApiPath\PathPartialUrl;

/**
 * Holds the relative paths to the individual endpoints and their operations. The path is appended to the URL from the
 * Server Object in order to construct the full URL
 * http://spec.openapis.org/oas/v3.0.3#path-item-object
 */

final class ApiPaths
{
    /** @var ApiPath[] */
    private array $paths;

    private function __construct(array $paths)
    {
        $this->paths = $paths;
    }

    public static function generate(): self
    {
        return new self([]);
    }

    private function findPathWithUrl(PathPartialUrl $url): ?ApiPath
    {
        foreach ($this->paths as $path) {
            if ($path->getUrl()->isIdenticalTo($url)) {
                return $path;
            }
        }
        return null;
    }

    private function removePath(PathPartialUrl $url): void
    {
        foreach ($this->paths as $index => $path) {
            if ($path->getUrl()->isIdenticalTo($url)) {
                unset($this->paths[$index]);
            }
        }
    }

    private function getOperationById(OperationId $id): ?PathOperation
    {
        foreach ($this->paths as $path) {
            $operation = $path->findOperationById($id);
            if ($operation) {
                return $operation;
            }
        }

        return null;
    }

    public function addPath(ApiPath $path): self
    {
        $existingPath = $this->findPathWithUrl($path->getUrl());
        if (!is_null($existingPath)) {
            $this->removePath($existingPath->getUrl());
            $path = $existingPath->mergeOperations($path->getOperations());
        }

        return new self(array_merge($this->paths, [$path]));
    }

    public function toArrayOfPaths(): array
    {
        return $this->paths;
    }

    public function toOpenApiSpecification(): array
    {
        $specifications = [];
        foreach ($this->paths as $path) {
            $specifications = array_merge($specifications, $path->toOpenApiSpecification());
        }
        ksort($specifications);
        return $specifications;
    }

    public function validate(): void
    {
        $allOperationIds = [];
        foreach ($this->paths as $path) {
            foreach ($path->getOperations()->toArrayOfOperations() as $operation) {
                $operationId = $operation->getId()->toString();
                if (
                in_array($operationId, $allOperationIds)
                ) {
                    throw SpecificationException::generatePathOperationIdAlreadyDefined($operationId);
                }
                $allOperationIds[] = $operationId;

                $this->validateLinks($operation);
            }
        }
    }

    private function validateLinks(PathOperation $sourceOperation): void
    {
        $responses = $sourceOperation->getResponses();
        $sourceParameters = $sourceOperation->getParameters();
        foreach ($responses->toArrayOfResponses() as $response) {
            if (!$response->hasLinks()) {
                continue;
            }

            foreach ($response->getLinks()->toArrayOfLinks() as $link) {
                $link = $link->toLink();
                $linkOperationId = $link->getOperationId()->toString();
                $targetOperation = $this->getOperationById(OperationId::fromString($linkOperationId));
                if (!$targetOperation) {
                    throw SpecificationException::generateTargetOperationDoesNotExist($linkOperationId, $sourceOperation->getId()->toString());
                }
                $linkParameters = $link->getParameters();
                if (!$linkParameters->isDefined()) {
                    throw SpecificationException::generateLinkHasNoParameters($linkOperationId, $sourceOperation->getId()->toString());
                }

                $targetParameters = $targetOperation->getParameters();
                foreach ($response->getDefinedMimeTypes() as $mimeType) {
                    $sourceRequestBodySchema = $sourceOperation->getRequestBodySchemaByMimeType($mimeType);
                    $targetRequestBodySchema = $targetOperation->getRequestBodySchemaByMimeType($mimeType);
                    $responseSchema = $response->toResponseSchema()->getSchemaByMimeType($mimeType);

                    foreach ($linkParameters->toArrayOfLinkParameters() as $parameter) {
                        $key = $parameter->getKey();
                        $keyName = $key->getName()->toString();
                        $targetSchema = null;
                        $targetParameter = null;
                        if ($key->isInRequestBody()) {
                            $targetSchema = $targetRequestBodySchema->findSchemaByName($keyName);
                        } elseif ($key->isInQuery()) {
                            $targetParameter = $targetParameters->getQueryParameter($keyName);
                        } elseif ($key->isInPath()) {
                            $targetParameter = $targetParameters->getPathParameter($keyName);
                        } elseif ($key->isInCookie()) {
                            $targetParameter = $targetParameters->getCookieParameter($keyName);
                        } elseif ($key->isInHeader()) {
                            $targetParameter = $targetParameters->getHeaderParameter($keyName);
                        }
                        if (is_null($targetParameter) && is_null($targetSchema)) {
                            throw SpecificationException::generateTargetParameterNotDefined($key->toOpenApiSpecification(), $linkOperationId, $sourceOperation->getId()->toString());
                        }
                        if (!is_null($targetParameter)) {
                            $targetSchema = $targetParameter->getSchema()->toSchema();
                        }

                        $value = $parameter->getValue();
                        $valueName = $value->getName()->toString();
                        $valueSchema = null;
                        $valueParameter = null;
                        if ($value->isInRequestBody()) {
                            $valueSchema = $sourceRequestBodySchema->findSchemaByName($valueName);
                        } elseif ($value->isInResponseBody()) {
                            $valueSchema = $responseSchema->findSchemaByName($valueName);
                        } elseif ($value->isInQuery()) {
                            $valueParameter = $sourceParameters->getQueryParameter($valueName);
                        } elseif ($value->isInPath()) {
                            $valueParameter = $sourceParameters->getPathParameter($valueName);
                        } elseif ($value->isInCookie()) {
                            $valueParameter = $sourceParameters->getCookieParameter($valueName);
                        } elseif ($value->isInHeader()) {
                            $valueParameter = $sourceParameters->getHeaderParameter($valueName);
                        }
                        if (is_null($valueParameter) && is_null($valueSchema)) {
                            throw SpecificationException::generateSourceParameterNotDefined($value->toOpenApiSpecification(), $sourceOperation->getId()->toString());
                        }
                        if (!is_null($valueParameter)) {
                            $valueSchema = $valueParameter->getSchema()->toSchema();
                        }

                        if (!$valueSchema->isCompatibleWith($targetSchema)) {
                            throw SpecificationException::generateParametersNotCompatible($key->toOpenApiSpecification(), $linkOperationId, $value->toOpenApiSpecification(), $sourceOperation->getId()->toString());
                        }
                    }
                }
            }
        }
    }
}