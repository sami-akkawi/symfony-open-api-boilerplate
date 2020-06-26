<?php declare(strict_types=1);

namespace App\OpenApiSpecification\ApiComponents\ComponentsSecurityScheme;

use App\OpenApiSpecification\ApiComponents\ComponentsSecurityScheme\SecurityScheme\OpenIdConnectUrl;
use App\OpenApiSpecification\ApiComponents\ComponentsSecurityScheme\SecurityScheme\SchemeDescription;
use App\OpenApiSpecification\ApiComponents\ComponentsSecurityScheme\SecurityScheme\SchemeName;
use App\OpenApiSpecification\ApiComponents\ComponentsSecurityScheme\SecurityScheme\SchemeType;
use App\OpenApiSpecification\ApiComponents\ComponentsSecurityScheme;

final class OpenIdConnectSecurityScheme extends ComponentsSecurityScheme
{
    private OpenIdConnectUrl $url;

    protected function __construct(SchemeName $name, OpenIdConnectUrl $url, ?SchemeDescription $description = null)
    {
        parent::__construct($name, SchemeType::generateOpenIdConnect(), $description);
        $this->url = $url;
    }

    public function setDescription(string $description): self
    {
        return new self($this->schemeName, $this->url, SchemeDescription::fromString($description));
    }

    public static function generate(string $name, string $url): self
    {
        return new self(SchemeName::fromString($name), OpenIdConnectUrl::fromString($url));
    }

    public function toOpenApiSpecification(): array
    {
        $specification = [
            'type' => $this->type->toString(),
            'openIdConnectUrl' => $this->url->toString()
        ];

        if ($this->description) {
            $specification['description'] = $this->description->toString();
        }

        return $specification;
    }
}