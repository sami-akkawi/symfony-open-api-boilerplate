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
    // todo: private Paths $paths
    private ?ApiServers $servers;
    private ?ApiComponents $components;
    private ?ApiSecurityRequirements $securityRequirements;
    private ?ApiTags $tags;

    public function __construct(
        OpenApiVersion $openApiVersion,
        ApiInfo $info,
        ?ApiServers $servers = null,
        ?ApiComponents $components = null,
        ?ApiSecurityRequirements $securityRequirements = null,
        ?ApiTags $tags = null
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
        $specifications = [
            'openapi' => $this->openApiVersion->toString(),
            'info' => $this->info->toOpenApiSpecification(),
            'paths' => []
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
        return json_encode($this->toOpenApiSpecification(), JSON_PRETTY_PRINT);
    }
}