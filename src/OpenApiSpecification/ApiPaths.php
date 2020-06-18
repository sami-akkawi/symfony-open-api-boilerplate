<?php declare(strict=1);

namespace App\OpenApiSpecification;

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

    public function addPath(ApiPath $path): self
    {
        $existingPath = $this->findPathWithUrl($path->getUrl());
        if (!is_null($existingPath)) {
            $this->removePath($existingPath->getUrl());
            $path = $existingPath->mergeOperations($path->getOperations());
        }

        return new self(array_merge($this->paths, [$path]));
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
}