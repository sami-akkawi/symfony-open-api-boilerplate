<?php declare(strict=1);

namespace App\ApiV1Bundle;

use App\ApiV1Bundle\ApiSpecification\ApiComponents;
use App\ApiV1Bundle\ApiSpecification\ApiComponents\ComponentsSecurityScheme\HttpSecurityScheme;
use App\ApiV1Bundle\ApiSpecification\ApiException\SpecificationException;
use App\ApiV1Bundle\ApiSpecification\ApiInfo;
use App\ApiV1Bundle\ApiSpecification\ApiInfo\Contact;
use App\ApiV1Bundle\ApiSpecification\ApiInfo\License;
use App\ApiV1Bundle\ApiSpecification\ApiInfo\Version;
use App\ApiV1Bundle\ApiSpecification\ApiSecurityRequirement;
use App\ApiV1Bundle\ApiSpecification\ApiSecurityRequirements;
use App\ApiV1Bundle\ApiSpecification\ApiServer;
use App\ApiV1Bundle\ApiSpecification\ApiServers;
use App\ApiV1Bundle\ApiSpecification\ApiServers\ServerVariable;
use App\ApiV1Bundle\ApiSpecification\ApiTag;
use App\ApiV1Bundle\ApiSpecification\ApiTags;
use App\ApiV1Bundle\ApiSpecification\OpenApiVersion;
use App\ApiV1Bundle\Schema\AbstractSchema;
use App\Kernel;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use SplFileInfo;
use Symfony\Component\HttpFoundation\Response;

final class SpecificationController
{
    public function show(): Response
    {
        try {
            $specification = new ApiSpecification(
                OpenApiVersion::generate(),
                $this->getInfo(),
                $this->getServers(),
                $this->getComponents(),
                $this->getSecurityRequirements(),
                $this->getTags()
            );
        } catch (SpecificationException $exception) {
            return new Response($exception->getMessage());
        }

        // todo: insert json into swagger/openApi UI
        // todo: cache file
        return new Response('<pre>' . $specification->toJson() . '</pre>');
    }

    private function getComponents(): ApiComponents
    {
        $components = ApiComponents::generate()
            ->addSecurityScheme(
                HttpSecurityScheme::generateBearer('JsonWebToken')->setBearerFormat('JWT')
            );

        $components = $this->addSchemas($components);

        return $components;
    }

    private function addSchemas(ApiComponents $components): ApiComponents
    {
        $type = 'schema';
        foreach ($this->getAutoLoadedClasses($type) as $file) {
            if (
                !$file->isFile() ||
                is_int(strpos($file->getBaseName(), 'AbstractSchema'))
            ) {
                continue;
            }

            /** @var AbstractSchema $schemaClass */
            $schemaClass = $this->getFullyQualifiedClassName($file, $type);
            $components = $components->addSchema(
                $schemaClass::getOpenApiSchema()
            );
        }

        return $components;
    }

    private function getAutoLoadedClasses(string $type): RecursiveIteratorIterator
    {
        $type = ucfirst($type);
        return new RecursiveIteratorIterator(new RecursiveDirectoryIterator($this->getDirectory($type)));
    }

    private function getFullyQualifiedClassName(SplFileInfo $file, string $type): ?string
    {
        $type = ucfirst($type);
        $normalizedPathName = str_replace(DIRECTORY_SEPARATOR, '\\', $file->getPathName());
        $relativePath = explode("\\ApiV1Bundle\\$type\\", $normalizedPathName)[1];
        $className = explode('.', $relativePath)[0];
        return $this->getNamespaceByType($type) . $className;
    }

    private function getNamespaceByType(string $type): string
    {
        return "App\\ApiV1Bundle\\$type\\";
    }

    private function getDirectory(string $type): string
    {
        $directoryParts = ['src', 'ApiV1Bundle', $type];
        return $this->getFullDirectory($directoryParts);
    }

    private function getFullDirectory(array $directoryParts): string
    {
        return Kernel::getKernelDir() . DIRECTORY_SEPARATOR . implode(DIRECTORY_SEPARATOR, $directoryParts) . DIRECTORY_SEPARATOR;
    }

    private function getInfo(): ApiInfo
    {
        return ApiInfo::generate('Boilerplate API')
            ->setDescription('This is a boilerplate API description.')
            ->setContact(Contact::generate()->setEmail('something@your-website.ch'))
            ->setLicense(License::generate('Apache 2.0'))
            ->setTermsOfService('https://www.your-website.ch/api-terms-of-service');
    }

    private function getSecurityRequirements(): ApiSecurityRequirements
    {
        return ApiSecurityRequirements::generate()
            ->addRequirement(ApiSecurityRequirement::generate('ApiKey'))
            ->addRequirement(ApiSecurityRequirement::generate('JWT'));
    }

    private function getServers(): ApiServers
    {
        return ApiServers::generate()
            ->addServer(
                ApiServer::generate('https://development.your-website.ch/v' . Version::getMajorVersion())
                    ->setDescription('Development Server')
                    ->addVariable(
                        ServerVariable::generate('username', 'admin')
                            ->addOptions(['support', 'SAA2020'])
                    )->addVariable(
                        ServerVariable::generate('port', '8888')
                    )
            )
            ->addServer(
                ApiServer::generate('https://platform-api.your-website.ch/v' . Version::getMajorVersion())
                    ->setDescription('Live Server')
            );
    }

    private function getTags(): ApiTags
    {
        return ApiTags::generate()
            ->addTag(
                ApiTag::generate('Security')
                    ->setDescription('Operations related to authentication and authorisation.')
            );
    }
}