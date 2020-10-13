<?php declare(strict_types=1);

namespace App\OpenApiSpecification;

use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

final class ApiSpecification
{
    private OpenApiVersion $openApiVersion;
    private ApiInfo $info;
    private ApiPaths $paths;
    private ApiServers $servers;
    private ApiComponents $components;
    private ApiSecurityRequirements $securityRequirements;
    private ApiTags $tags;
    private ?ApiExternalDocs $externalDocs;

    public function __construct(
        OpenApiVersion $openApiVersion,
        ApiInfo $info,
        ApiPaths $paths,
        ApiServers $servers,
        ApiComponents $components,
        ApiSecurityRequirements $securityRequirements,
        ApiTags $tags,
        ?ApiExternalDocs $externalDocs
    ) {
        $paths->validate();
        $this->openApiVersion = $openApiVersion;
        $this->info = $info;
        $this->paths = $paths;
        $this->servers = $servers;
        $this->components = $components;
        $this->securityRequirements = $securityRequirements;
        $this->tags = $tags;
        $this->externalDocs = $externalDocs;
    }

    public function toOpenApiSpecification(): array
    {
        $specifications = [
            'openapi' => $this->openApiVersion->toString(),
            'info' => $this->info->toOpenApiSpecification(),
            'paths' => $this->paths->toOpenApiSpecification(),
        ];

        if ($this->components->isDefined()) {
            $specifications['components'] = $this->components->toOpenApiSpecification();
        }

        if ($this->servers->isDefined()) {
            $specifications['servers'] = $this->servers->toOpenApiSpecification();
        }

        $specifications['security'] = $this->securityRequirements->toOpenApiSpecification();
        $specifications['tags'] = $this->tags->toOpenApiSpecification();

        if ($this->externalDocs) {
            $specifications['externalDocs'] = $this->externalDocs->toOpenApiSpecification();
        }

        return $specifications;
    }

    public function toSymfonyRouteCollection(string $serviceMethodName): RouteCollection
    {
        $collection = new RouteCollection();
        foreach ($this->paths->toArrayOfPaths() as $path) {
            foreach ($path->getOperations()->toArrayOfOperations() as $operation) {
                $method = $operation->getName()->toString();
                $id = $operation->getId()->toString();
                $url = 'v' . $this->info->getMajorVersion() . $path->getUrl()->toString();

                $requirements = [];
                $pathParts = explode('/', $url);
                foreach ($pathParts as $part) {
                    if (strpos($part, '{') === 0 && strpos($part, '}') === strlen($part) - 1) {
                        $part = str_replace('{', '', $part);
                        $parameterName = str_replace('}', '', $part);
                        $parameter = $operation->getPathParameter($parameterName);
                        $parameterRouteRequirements = $parameter->getRouteRequirements();
                        if ($parameterRouteRequirements) {
                            $requirements[$parameterName] = "$parameterRouteRequirements";
                        }
                    }
                }

                $defaults = [
                    '_controller' => "$id::$serviceMethodName"
                ];

                $options = [];
                $host = '';
                $schemes = ['http', 'https'];
                $methods = [$method];
                $collection->add(
                    $id,
                    new Route($url, $defaults, $requirements, $options, $host, $schemes, $methods)
                );
            }
        }

        return $collection;
    }

    public function toJson(): string
    {
        return json_encode($this->toOpenApiSpecification(), JSON_PRETTY_PRINT);
    }

    public function toYaml(): string
    {
        return yaml_emit($this->toOpenApiSpecification(), YAML_UTF8_ENCODING);
    }
}