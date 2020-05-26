<?php declare(strict=1);

namespace App\ApiV1Bundle;

use App\ApiV1Bundle\ApiSpecification\ApiComponents;
use App\ApiV1Bundle\ApiSpecification\ApiInfo;
use App\ApiV1Bundle\ApiSpecification\OpenApiVersion;
use App\ApiV1Bundle\ApiSpecification\ApiSecurityRequirements;
use App\ApiV1Bundle\ApiSpecification\ApiServers;
use App\ApiV1Bundle\ApiSpecification\ApiTags;

final class ApiSpecification
{
    private OpenApiVersion $openApiVersion;
    private ApiInfo $info;
    private ApiServers $servers;
    // todo: paths
    private ApiComponents $components;
    private ApiSecurityRequirements $securityRequirements;
    private ApiTags $tags;

    public function __construct(
        OpenApiVersion $openApiVersion,
        ApiInfo $info,
        ApiServers $servers,
        ApiComponents $components,
        ApiSecurityRequirements $securityRequirements,
        ApiTags $tags
    ) {
        $this->openApiVersion = $openApiVersion;
        $this->info = $info;
        $this->servers = $servers;
        $this->components = $components;
        $this->securityRequirements = $securityRequirements;
        $this->tags = $tags;
    }

    public function toOpenApiSpecification(): array
    {
        return [
            'openapi' => $this->openApiVersion->toString(),
            'info' => $this->info->toOpenApiSpecification(),
            'paths' => [],
            'components' => $this->components->toOpenApiSpecification(),
            'servers' => $this->servers->toOpenApiSpecification(),
            'security' => $this->securityRequirements->toOpenApiSpecification(),
            'tags' => $this->tags->toOpenApiSpecification(),
        ];
    }

    public function toJson(): string
    {
        return json_encode($this->toOpenApiSpecification(), JSON_PRETTY_PRINT);
    }
}