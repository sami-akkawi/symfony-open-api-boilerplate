<?php declare(strict=1);

namespace App\ApiV1Bundle\ApiSpecification\ApiInfo;

use App\ApiV1Bundle\ApiSpecification\ApiInfo\License\LicenseName;
use App\ApiV1Bundle\ApiSpecification\ApiInfo\License\LicenseUrl;

/**
 * The license information for the exposed API.
 * http://spec.openapis.org/oas/v3.0.3#info-object
 */

final class License
{
    private LicenseName $name;
    private ?LicenseUrl $url;

    private function __construct(LicenseName $name, ?LicenseUrl $url)
    {
        $this->name = $name;
        $this->url = $url;
    }

    public static function generate(string $name): self
    {
        return new self(LicenseName::fromString($name), null);
    }

    public function setUrl(string $url): self
    {
        return new self(
            $this->name,
            LicenseUrl::fromString($url)
        );
    }

    public function toOpenApiSpecification(): array
    {
        $specification = [
            'name' => $this->name->toString()
        ];
        if ($this->url) {
            $specification['url'] = $this->url->toString();
        }
        return $specification;
    }
}