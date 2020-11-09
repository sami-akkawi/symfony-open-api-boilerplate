<?php declare(strict_types=1);

namespace App\ApiV1Bundle\Helpers;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;

final class ApiV1Request
{
    const PATH_PARAMETER = 'path';
    const QUERY_PARAMETER = 'query';
    const BODY_PARAMETER = 'body';
    const HEADER_PARAMETER = 'header';
    const COOKIE_PARAMETER = 'cookie';

    private Request $request;
    private string $uri;
    private array $pathParams;
    private array $requestBody;
    private array $queryParams;
    private array $headerParams;
    private array $cookieParams;
    /** @var UploadedFile[] */
    private array $files;

    public function __construct(
        Request $request,
        array $pathParams,
        array $requestBody,
        array $queryParams,
        array $headerParams,
        array $cookieParams
    ) {
        $this->request = $request;
        $this->uri = $request->getUri();
        $this->pathParams = $pathParams;
        $this->requestBody = $requestBody;
        $this->queryParams = $queryParams;
        $this->headerParams = $headerParams;
        $this->cookieParams = $cookieParams;
        $this->files = $this->extractFiles($request->files->all());
    }

    private function extractFiles(array $files): array
    {
        $simpleFiles = [];
        foreach ($files as $file) {
            if (is_array($file)) {
                $simpleFiles = array_merge($simpleFiles, $this->extractFiles($file));
                continue;
            }
            $simpleFiles[] = $file;
        }
        return $simpleFiles;
    }

    public function getUri(): string
    {
        return $this->uri;
    }

    public function getPathParams(): array
    {
        return $this->pathParams;
    }

    public function getRequestBody(): array
    {
        return $this->requestBody;
    }

    public function getQueryParams(): array
    {
        return $this->queryParams;
    }

    public function getHeaderParams(): array
    {
        return $this->headerParams;
    }

    public function getCookieParams(): array
    {
        return $this->cookieParams;
    }

    public function getFiles(): array
    {
        return $this->files;
    }

    public function getOriginalRequest(): Request
    {
        return $this->request;
    }
}