<?php declare(strict=1);

namespace App\ApiV1Bundle;

use App\ApiV1Bundle\Specification\Info;
use App\ApiV1Bundle\Specification\Info\Contact;
use App\ApiV1Bundle\Specification\Info\License;
use App\ApiV1Bundle\Specification\OpenApiVersion;

final class Specification
{
    private OpenApiVersion $openApiVersion;
    private Info $info;

    public function __construct() {
        $this->openApiVersion = OpenApiVersion::generate();
        $this->info = $this->getInfo();
    }

    public function toArray(): array
    {
        return [
            'openapi' => $this->openApiVersion->toString(),
            'info' => $this->info->toOpenApiSpecification()
        ];
    }

    private function getInfo(): Info
    {
        return Info::generate('Boilerplate API')
            ->setDescription('This is a boilerplate API description.')
            ->setContact(Contact::generate()->setEmail('something@your-website.ch'))
            ->setLicense(License::generate('Apache 2.0'))
            ->setTermsOfService('https://www.your-website.ch/api-terms-of-service');
    }

    public function toJson(): string
    {
        return json_encode($this->toArray(), JSON_PRETTY_PRINT);
    }
}