<?php declare(strict=1);

namespace App\ApiV1Bundle\ApiSpecification;

use App\ApiV1Bundle\ApiSpecification\ApiInfo\Contact;
use App\ApiV1Bundle\ApiSpecification\ApiInfo\Description;
use App\ApiV1Bundle\ApiSpecification\ApiInfo\License;
use App\ApiV1Bundle\ApiSpecification\ApiInfo\TermsOfService;
use App\ApiV1Bundle\ApiSpecification\ApiInfo\Title;
use App\ApiV1Bundle\ApiSpecification\ApiInfo\Version;

/**
 * REQUIRED. Provides metadata about the API. The metadata MAY be used by tooling as required.
 * http://spec.openapis.org/oas/v3.0.3#fixed-fields
 */

final class ApiInfo
{
    private Title $title;
    private ?Description $description;
    private ?TermsOfService $termsOfService;
    private ?Contact $contact;
    private ?License $license;

    private function __construct(
        Title $title,
        ?Description $description = null,
        ?TermsOfService $termsOfService = null,
        ?Contact $contact = null,
        ?License $license = null
    ) {
        $this->title = $title;
        $this->description = $description;
        $this->termsOfService = $termsOfService;
        $this->contact = $contact;
        $this->license = $license;
    }

    public static function generate(string $title): self
    {
        return new self(Title::fromString($title));
    }

    public function setContact(Contact $contact): self
    {
        return new self(
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
            $this->title,
            $this->description,
            $this->termsOfService,
            $this->contact,
            $license
        );
    }

    public function setTermsOfService(string $termsOfService): self
    {
        return new self(
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
            'version' => Version::getVersion()
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