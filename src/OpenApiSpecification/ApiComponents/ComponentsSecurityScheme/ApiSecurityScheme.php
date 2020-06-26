<?php declare(strict_types=1);

namespace App\OpenApiSpecification\ApiComponents\ComponentsSecurityScheme;

use App\OpenApiSpecification\ApiComponents\ComponentsSecurityScheme\SecurityScheme\ApiKeyLocation;
use App\OpenApiSpecification\ApiComponents\ComponentsSecurityScheme\SecurityScheme\ApiKeyName;
use App\OpenApiSpecification\ApiComponents\ComponentsSecurityScheme\SecurityScheme\SchemeName;
use App\OpenApiSpecification\ApiComponents\ComponentsSecurityScheme\SecurityScheme\SchemeDescription;
use App\OpenApiSpecification\ApiComponents\ComponentsSecurityScheme\SecurityScheme\SchemeType;
use App\OpenApiSpecification\ApiComponents\ComponentsSecurityScheme;

final class ApiSecurityScheme extends ComponentsSecurityScheme
{
    private ApiKeyName $apiKeyName;
    private ApiKeyLocation $location;

    protected function __construct(
        SchemeName $schemeName,
        ApiKeyName $apiKeyName,
        ApiKeyLocation $location,
        ?SchemeDescription $description = null
    ) {
        parent::__construct($schemeName, SchemeType::generateApiKey());
        $this->apiKeyName = $apiKeyName;
        $this->description = $description;
        $this->location = $location;
    }

    private static function generate(string $schemeName, string $apiKeyName, ApiKeyLocation $location): self
    {
        return new self(SchemeName::fromString($schemeName), ApiKeyName::fromString($apiKeyName), $location);
    }

    public static function generateInQuery(string $schemeName, string $apiKeyName): self
    {
        return self::generate($schemeName, $apiKeyName, ApiKeyLocation::generateInQuery());
    }

    public static function generateInHeader(string $schemeName, string $apiKeyName): self
    {
        return self::generate($schemeName, $apiKeyName, ApiKeyLocation::generateInHeader());
    }

    public static function generateInCookie(string $schemeName, string $apiKeyName): self
    {
        return self::generate($schemeName, $apiKeyName, ApiKeyLocation::generateInCookie());
    }

    public function setDescription(string $description): self
    {
        return new self($this->schemeName, $this->apiKeyName, $this->location, SchemeDescription::fromString($description));
    }

    public function toOpenApiSpecification(): array
    {
        $specification = [
            'type' => $this->type->toString(),
            'name' => $this->apiKeyName->toString(),
            'in' => $this->location->toString()
        ];

        if ($this->description) {
            $specification['description'] = $this->description->toString();
        }

        return $specification;
    }
}