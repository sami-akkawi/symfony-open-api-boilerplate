<?php declare(strict_types=1);

namespace App\OpenApiSpecification\ApiInfo;

use App\OpenApiSpecification\ApiInfo\InfoContact\ContactEmail;
use App\OpenApiSpecification\ApiInfo\InfoContact\ContactName;
use App\OpenApiSpecification\ApiInfo\InfoContact\ContactUrl;

/**
 * The contact information for the exposed API.
 * http://spec.openapis.org/oas/v3.0.3#info-object
 */

final class InfoContact
{
    private ?ContactName $name;
    private ?ContactEmail $email;
    private ?ContactUrl $url;

    private function __construct(
        ?ContactName $name = null,
        ?ContactEmail $email = null,
        ?ContactUrl $url = null
    ) {
        $this->name = $name;
        $this->email = $email;
        $this->url = $url;
    }

    public static function generate(): self
    {
        return new self();
    }

    public function setName(string $name): self
    {
        return new self(
            ContactName::fromString($name),
            $this->email,
            $this->url
        );
    }

    public function setEmail(string $email): self
    {
        return new self(
            $this->name,
            ContactEmail::fromString($email),
            $this->url
        );
    }

    public function setUrl(string $url): self
    {
        return new self(
            $this->name,
            $this->email,
            ContactUrl::fromString($url)
        );
    }

    public function toOpenApiSpecification(): array
    {
        $specification = [];
        if ($this->name) {
            $specification['name'] = $this->name->toString();
        }
        if ($this->email) {
            $specification['email'] = $this->email->toString();
        }
        if ($this->url) {
            $specification['url'] = $this->url->toString();
        }
        return $specification;
    }

    public function isDefined(): bool
    {
        return ($this->name || $this->email || $this->url);
    }
}