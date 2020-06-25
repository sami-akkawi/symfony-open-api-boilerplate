<?php declare(strict_types=1);

namespace App\OpenApiSpecification;

use App\OpenApiSpecification\ApiInfo\InfoContact;
use App\OpenApiSpecification\ApiInfo\InfoDescription;
use App\OpenApiSpecification\ApiInfo\InfoLicense;
use App\OpenApiSpecification\ApiInfo\InfoTermsOfService;
use App\OpenApiSpecification\ApiInfo\InfoTitle;
use App\OpenApiSpecification\ApiInfo\InfoVersion;

/**
 * REQUIRED. Provides metadata about the API. The metadata MAY be used by tooling as required.
 * http://spec.openapis.org/oas/v3.0.3#fixed-fields
 */

final class ApiInfo
{
    private InfoVersion $version;
    private InfoTitle $title;
    private ?InfoDescription $description;
    private ?InfoTermsOfService $termsOfService;
    private ?InfoContact $contact;
    private ?InfoLicense $license;

    private function __construct(
        InfoVersion $version,
        InfoTitle $title,
        ?InfoDescription $description = null,
        ?InfoTermsOfService $termsOfService = null,
        ?InfoContact $contact = null,
        ?InfoLicense $license = null
    ) {
        $this->version = $version;
        $this->title = $title;
        $this->description = $description;
        $this->termsOfService = $termsOfService;
        $this->contact = $contact;
        $this->license = $license;
    }

    public static function generate(string $title, InfoVersion $version): self
    {
        return new self($version, InfoTitle::fromString($title));
    }

    public function setContact(InfoContact $contact): self
    {
        return new self(
            $this->version,
            $this->title,
            $this->description,
            $this->termsOfService,
            $contact,
            $this->license
        );
    }

    public function setDescription(string $description): self
    {
        return new self(
            $this->version,
            $this->title,
            InfoDescription::fromString($description),
            $this->termsOfService,
            $this->contact,
            $this->license
        );
    }

    public function setLicense(InfoLicense $license): self
    {
        return new self(
            $this->version,
            $this->title,
            $this->description,
            $this->termsOfService,
            $this->contact,
            $license
        );
    }

    public function getMajorVersion(): string
    {
        return $this->version->getMajorVersion();
    }

    public function setTermsOfService(string $termsOfService): self
    {
        return new self(
            $this->version,
            $this->title,
            $this->description,
            InfoTermsOfService::fromString($termsOfService),
            $this->contact,
            $this->license
        );
    }

    public function toOpenApiSpecification(): array
    {
        $specification = [
            'title' => $this->title->toString(),
            'version' => $this->version->getFullVersion()
        ];
        if ($this->description) {
            $specification['description'] = $this->description->toString();
        }
        if ($this->termsOfService) {
            $specification['termsOfService'] = $this->termsOfService->toString();
        }
        if ($this->contact && $this->contact->isDefined()) {
            $specification['contact'] = $this->contact->toOpenApiSpecification();
        }
        if ($this->license) {
            $specification['license'] = $this->license->toOpenApiSpecification();
        }
        return $specification;
    }
}