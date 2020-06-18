<?php declare(strict=1);

namespace App\OpenApiSpecification;

final class ApiSpecification
{
    private OpenApiVersion $openApiVersion;
    private ApiInfo $info;
    private ApiPaths $paths;
    private ApiServers $servers;
    private ApiComponents $components;
    private ApiSecurityRequirements $securityRequirements;
    private ApiTags $tags;

    public function __construct(
        OpenApiVersion $openApiVersion,
        ApiInfo $info,
        ApiPaths $paths,
        ApiServers $servers,
        ApiComponents $components,
        ApiSecurityRequirements $securityRequirements,
        ApiTags $tags
    ) {
        $this->openApiVersion = $openApiVersion;
        $this->info = $info;
        $this->paths = $paths;
        $this->servers = $servers;
        $this->components = $components;
        $this->securityRequirements = $securityRequirements;
        $this->tags = $tags;
    }

    public function toOpenApiSpecification(): array
    {
        $specifications = [
            'openapi' => $this->openApiVersion->toString(),
            'info' => $this->info->toOpenApiSpecification(),
            'paths' => $this->paths->toOpenApiSpecification(),
        ];

        if ($this->components && $this->components->isDefined()) {
            $specifications['components'] = $this->components->toOpenApiSpecification();
        }

        if ($this->servers && $this->servers->isDefined()) {
            $specifications['servers'] = $this->servers->toOpenApiSpecification();
        }

        if ($this->securityRequirements) {
            $specifications['security'] = $this->securityRequirements->toOpenApiSpecification();
        }

        if ($this->tags) {
            $specifications['tags'] = $this->tags->toOpenApiSpecification();
        }

        return $specifications;
    }

    public function toJson(): string
    {
        return json_encode($this->toOpenApiSpecification());
    }
}