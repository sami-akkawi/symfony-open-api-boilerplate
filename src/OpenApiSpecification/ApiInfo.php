<?php declare(strict_types=1);

namespace App\OpenApiSpecification;

use App\OpenApiSpecification\ApiInfo\Contact;
use App\OpenApiSpecification\ApiInfo\Description;
use App\OpenApiSpecification\ApiInfo\License;
use App\OpenApiSpecification\ApiInfo\TermsOfService;
use App\OpenApiSpecification\ApiInfo\Title;
use App\OpenApiSpecification\ApiInfo\Version;

/**
 * REQUIRED. Provides metadata about the API. The metadata MAY be used by tooling as required.
 * http://spec.openapis.org/oas/v3.0.3#fixed-fields
 */

final class ApiInfo
{
    private Version $version;
    private Title $title;
    private ?Description $description;
    private ?TermsOfService $termsOfService;
    private ?Contact $contact;
    private ?License $license;

    private function __construct(
        Version $version,
        Title $title,
        ?Description $description = null,
        ?TermsOfService $termsOfService = null,
        ?Contact $contact = null,
        ?License $license = null
    ) {
        $this->version = $version;
        $this->title = $title;
        $this->description = $description;
        $this->termsOfService = $termsOfService;
        $this->contact = $contact;
        $this->license = $license;
    }

    public static function generate(string $title, Version $version): self
    {
        return new self($version, Title::fromString($title));
    }

    public function setContact(Contact $contact): self
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
            Description::fromString($description),
            $this->termsOfService,
            $this->contact,
            $this->license
        );
    }

    public function setLicense(License $license): self
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
            TermsOfService::fromString($termsOfService),
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