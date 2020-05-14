<?php declare(strict=1);

namespace App\ApiV1Bundle\Specification\Info;

use App\ApiV1Bundle\Specification\Info\License\LicenseName;
use App\ApiV1Bundle\Specification\Info\License\LicenseUrl;

/**
 * The license information for the exposed API.
 * https://swagger.io/specification/#info-object
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