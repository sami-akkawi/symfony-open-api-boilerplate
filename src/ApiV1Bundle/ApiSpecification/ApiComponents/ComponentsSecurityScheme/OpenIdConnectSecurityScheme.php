<?php declare(strict=1);
// Created by sami-akkawi on 10.05.20

namespace App\ApiV1Bundle\ApiSpecification\ApiComponents\ComponentsSecurityScheme;

use App\ApiV1Bundle\ApiSpecification\ApiComponents\ComponentsSecurityScheme\SecurityScheme\OpenIdConnectUrl;
use App\ApiV1Bundle\ApiSpecification\ApiComponents\ComponentsSecurityScheme\SecurityScheme\SchemeDescription;
use App\ApiV1Bundle\ApiSpecification\ApiComponents\ComponentsSecurityScheme\SecurityScheme\SchemeName;
use App\ApiV1Bundle\ApiSpecification\ApiComponents\ComponentsSecurityScheme\SecurityScheme\SchemeType;
use App\ApiV1Bundle\ApiSpecification\ApiComponents\SecurityScheme;

final class OpenIdConnectSecurityScheme extends SecurityScheme
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