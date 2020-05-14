<?php declare(strict=1);

namespace App\ApiV1Bundle;

use App\ApiV1Bundle\Specification\Info;
use App\ApiV1Bundle\Specification\Info\Contact;
use App\ApiV1Bundle\Specification\Info\License;
use App\ApiV1Bundle\Specification\Info\Version;
use App\ApiV1Bundle\Specification\OpenApiVersion;
use App\ApiV1Bundle\Specification\Server;
use App\ApiV1Bundle\Specification\Servers;
use App\ApiV1Bundle\Specification\Servers\ServerVariable;

final class Specification
{
    private OpenApiVersion $openApiVersion;
    private Info $info;
    private Servers $servers;
    // todo: paths
    // todo: components
    // todo: securityRequirements
    // todo: tags

    public function __construct() {
        $this->openApiVersion = OpenApiVersion::generate();
        $this->info = $this->getInfo();
        $this->servers = $this->getServers();
    }

    public function toArray(): array
    {
        return [
            'openapi' => $this->openApiVersion->toString(),
            'info' => $this->info->toOpenApiSpecification(),
            'servers' => $this->servers->toOpenApiSpecification(),
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

    private function getServers(): Servers
    {
        return Servers::generate()
            ->addServer(
                Server::generate('https://development.your-website.ch/v' . Version::getMajorVersion())
                    ->setDescription('Development Server')
                    ->addVariable(
                        ServerVariable::generate('username', 'admin')
                            ->addOptions(['support', 'SAA2020'])
                    )->addVariable(
                        ServerVariable::generate('port', '8888')
                    )
            )
            ->addServer(
                Server::generate('https://platform-api.your-website.ch/v' . Version::getMajorVersion())
                    ->setDescription('Live Server')
            );
    }

    public function toJson(): string
    {
        return json_encode($this->toArray(), JSON_PRETTY_PRINT);
    }
}