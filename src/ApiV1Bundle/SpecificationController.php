<?php declare(strict_types=1);

namespace App\ApiV1Bundle;

use App\ApiV1Bundle\Endpoint\AbstractEndpoint;
use App\ApiV1Bundle\Example\AbstractExample;
use App\ApiV1Bundle\Header\AbstractHeader;
use App\ApiV1Bundle\Link\AbstractLink;
use App\ApiV1Bundle\Parameter\AbstractParameter;
use App\ApiV1Bundle\RequestBody\AbstractRequestBody;
use App\ApiV1Bundle\Response\AbstractResponse;
use App\ApiV1Bundle\Schema\AbstractSchema;
use App\ApiV1Bundle\Tag\AbstractTag;
use App\Kernel;
use App\OpenApiSpecification\ApiComponents;
use App\OpenApiSpecification\ApiComponents\ComponentsSecurityScheme\HttpSecurityScheme;
use App\OpenApiSpecification\ApiExternalDocs;
use App\OpenApiSpecification\ApiInfo;
use App\OpenApiSpecification\ApiInfo\InfoContact;
use App\OpenApiSpecification\ApiInfo\InfoLicense;
use App\OpenApiSpecification\ApiPaths;
use App\OpenApiSpecification\ApiSecurityRequirement;
use App\OpenApiSpecification\ApiSecurityRequirements;
use App\OpenApiSpecification\ApiServer;
use App\OpenApiSpecification\ApiServers;
use App\OpenApiSpecification\ApiServers\ServerVariable;
use App\OpenApiSpecification\ApiSpecification;
use App\OpenApiSpecification\ApiInfo\InfoVersion;
use App\OpenApiSpecification\ApiTags;
use App\OpenApiSpecification\OpenApiVersion;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use SplFileInfo;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Uid\Uuid;
use Symfony\Contracts\Cache\CacheInterface;

final class SpecificationController extends AbstractController
{
    private CacheInterface $cacheInterface;

    public function __construct(CacheInterface $cacheInterface)
    {
        $this->cacheInterface = $cacheInterface;
    }

    public function getApiSpecification(): ApiSpecification
    {
        return new ApiSpecification(
            OpenApiVersion::generate(),
            $this->getInfo(),
            $this->getPaths(),
            $this->getServers(),
            $this->getComponents(),
            $this->getSecurityRequirements(),
            $this->getTags(),
            $this->getApiExternalDocs()
        );
    }

    private function getCacheKey(string $section): string
    {
        return $_ENV['APP_ENV'] === 'prod'
            ? "{$section}-v{$this->getVersion()->getFullVersion()}"
            : Uuid::v4()->toRfc4122();
    }

    public function showJson(): Response
    {
        return $this->cacheInterface->get(
            $this->getCacheKey('api-json-specification'),
            fn() => new Response($this->getApiSpecification()->toJson(), 200, ['Access-Control-Allow-Origin' => '*'])
        );
    }

    public function showReadableJsonSpecs(): Response
    {
        return $this->cacheInterface->get(
            $this->getCacheKey('readable-json-api-specs'),
            fn() => new Response('<pre>' . $this->getApiSpecification()->toJson() . '</pre>')
        );
    }

    public function showYaml(): Response
    {
        return $this->cacheInterface->get(
            $this->getCacheKey('api-yaml-specification'),
            fn() => new Response($this->getApiSpecification()->toYaml(), 200, ['Access-Control-Allow-Origin' => '*'])
        );
    }

    public function showReadableYamlSpecs(): Response
    {
        return $this->cacheInterface->get(
            $this->getCacheKey('readable-yaml-api-specs'),
            fn() => new Response('<pre>' . $this->getApiSpecification()->toYaml() . '</pre>')
        );
    }

    public function showDocs(): Response
    {
        return $this->cacheInterface->get(
            $this->getCacheKey('api-docs'),
            fn() => $this->render('api/v1/docs.html.twig')
        );
    }

    private function getPaths(): ApiPaths
    {
        $paths = ApiPaths::generate();
        $type = 'endpoint';
        foreach ($this->getAutoLoadedClasses($type) as $file) {
            if (
                !$file->isFile()
                || is_int(strpos($file->getBaseName(), 'Abstract'))
            ) {
                continue;
            }

            /** @var AbstractEndpoint $endpointClass */
            $endpointClass = $this->getFullyQualifiedClassName($file, $type);
            $paths = $paths->addPath(
                $endpointClass::getApiPath()
            );
        }

        return $paths;
    }

    private function getComponents(): ApiComponents
    {
        $components = ApiComponents::generate()
            ->addSecurityScheme(
                HttpSecurityScheme::generateBearer('JsonWebToken')->setBearerFormat('JWT')
            );

        $components = $this->addSchemas($components);
        $components = $this->addResponses($components);
        $components = $this->addParameters($components);
        $components = $this->addExamples($components);
        $components = $this->addRequestBodies($components);
        $components = $this->addHeaders($components);
        $components = $this->addLinks($components);

        return $components;
    }

    private function addSchemas(ApiComponents $components): ApiComponents
    {
        $type = 'schema';
        foreach ($this->getAutoLoadedClasses($type) as $file) {
            if (
                !$file->isFile()
                || is_int(strpos($file->getBaseName(), 'AbstractSchema'))
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

    private function addResponses(ApiComponents $components): ApiComponents
    {
        $type = 'response';
        foreach ($this->getAutoLoadedClasses($type) as $file) {
            if (
                !$file->isFile()
                || is_int(strpos($file->getBaseName(), 'Abstract'))
            ) {
                continue;
            }

            /** @var AbstractResponse $responseClass */
            $responseClass = $this->getFullyQualifiedClassName($file, $type);
            $components = $components->addResponse(
                $responseClass::getOpenApiResponse()
            );
        }

        return $components;
    }

    private function addParameters(ApiComponents $components): ApiComponents
    {
        $type = 'parameter';
        foreach ($this->getAutoLoadedClasses($type) as $file) {
            if (
                !$file->isFile()
                || is_int(strpos($file->getBaseName(), 'AbstractParameter'))
            ) {
                continue;
            }

            /** @var AbstractParameter $parameterClass */
            $parameterClass = $this->getFullyQualifiedClassName($file, $type);
            $components = $components->addParameter(
                $parameterClass::getOpenApiParameter()
            );
        }

        return $components;
    }

    private function addExamples(ApiComponents $components): ApiComponents
    {
        $type = 'example';
        foreach ($this->getAutoLoadedClasses($type) as $file) {
            if (
                !$file->isFile()
                || is_int(strpos($file->getBaseName(), 'AbstractExample'))
            ) {
                continue;
            }

            /** @var AbstractExample $exampleClass */
            $exampleClass = $this->getFullyQualifiedClassName($file, $type);
            $components = $components->addExample(
                $exampleClass::getOpenApiExample()
            );
        }

        return $components;
    }

    private function addRequestBodies(ApiComponents $components): ApiComponents
    {
        $type = 'requestBody';
        foreach ($this->getAutoLoadedClasses($type) as $file) {
            if (
                !$file->isFile()
                || is_int(strpos($file->getBaseName(), 'AbstractRequestBody'))
            ) {
                continue;
            }

            /** @var AbstractRequestBody $exampleClass */
            $exampleClass = $this->getFullyQualifiedClassName($file, $type);
            $components = $components->addRequestBody(
                $exampleClass::getOpenApiRequestBody()
            );
        }

        return $components;
    }

    private function addHeaders(ApiComponents $components): ApiComponents
    {
        $type = 'header';
        foreach ($this->getAutoLoadedClasses($type) as $file) {
            if (
                !$file->isFile()
                || is_int(strpos($file->getBaseName(), 'AbstractHeader'))
            ) {
                continue;
            }

            /** @var AbstractHeader $headerClass */
            $headerClass = $this->getFullyQualifiedClassName($file, $type);
            $components = $components->addHeader(
                $headerClass::getOpenApiHeader()
            );
        }

        return $components;
    }

    private function addLinks(ApiComponents $components): ApiComponents
    {
        $type = 'link';
        foreach ($this->getAutoLoadedClasses($type) as $file) {
            if (
                !$file->isFile()
                || is_int(strpos($file->getBaseName(), 'AbstractLink'))
            ) {
                continue;
            }

            /** @var AbstractLink $linkClass */
            $linkClass = $this->getFullyQualifiedClassName($file, $type);
            $components = $components->addLink(
                $linkClass::getOpenApiLink()
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
        $relativePath = explode("\\$type\\", $normalizedPathName)[1];
        $className = explode('.', $relativePath)[0];
        return $this->getNamespaceByType($type) . $className;
    }

    private function getNamespaceByType(string $type): string
    {
        return __NAMESPACE__ . "\\$type\\";
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
        return ApiInfo::generate('Boilerplate API', $this->getVersion())
            ->setDescription('This is a boilerplate API description.')
            ->setContact(InfoContact::generate()->setEmail('something@your-website.ch'))
            ->setLicense(InfoLicense::generate('Apache 2.0'))
            ->setTermsOfService('https://www.your-website.ch/api-terms-of-service');
    }

    private function getSecurityRequirements(): ApiSecurityRequirements
    {
        return ApiSecurityRequirements::generate()
            ->addRequirement(ApiSecurityRequirement::generate('JsonWebToken'));
    }

    public function getVersion(): InfoVersion
    {
        return InfoVersion::generate(1, 0, 0);
    }

    private function getServers(): ApiServers
    {
        $majorVersion = $this->getVersion()->getMajorVersion();
        return ApiServers::generate()
            ->addServer(
                ApiServer::generate('/v' . $majorVersion)
                    ->setDescription('Dev Server')
            );
    }

    private function getTags(): ApiTags
    {
        $tags = ApiTags::generate();
        $type = 'tag';
        foreach ($this->getAutoLoadedClasses($type) as $file) {
            if (
                !$file->isFile()
                || is_int(strpos($file->getBaseName(), 'Abstract'))
            ) {
                continue;
            }

            /** @var AbstractTag $tagClass */
            $tagClass = $this->getFullyQualifiedClassName($file, $type);
            $tags = $tags->addTag(
                $tagClass::getApiTag()
            );
        }

        return $tags;
    }

    private function getApiExternalDocs(): ?ApiExternalDocs
    {
        return ApiExternalDocs::generate('https://www.example.com/')
            ->setDescription('Find out more about my service.');
    }
}