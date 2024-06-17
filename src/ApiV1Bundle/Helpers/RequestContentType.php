<?php declare(strict_types=1);

namespace App\ApiV1Bundle\Helpers;

final class RequestContentType
{
    const APPLICATION_XML  = 'application/xml';
    const APPLICATION_JSON = 'application/json';
    const FORM_MULTIPART   = 'form/multipart';
    const FORM_URLENCODED  = 'application/x-www-form-urlencoded';

    private string $requestContentType;

    public function __construct(?string $requestContentType)
    {
        $requestContentType = $requestContentType ?? 'empty';
        $isJsonRequest = str_contains($requestContentType, self::APPLICATION_JSON);
        $isXmlRequest  = str_contains($requestContentType, self::APPLICATION_XML);
        $isMultipartFormRequest = str_contains($requestContentType, self::FORM_MULTIPART);
        $isUrlencodedFormRequest = str_contains($requestContentType, self::FORM_URLENCODED);

        if ($isJsonRequest) {
            $this->requestContentType = self::APPLICATION_JSON;
        } elseif ($isXmlRequest) {
            $this->requestContentType = self::APPLICATION_XML;
        } elseif ($isMultipartFormRequest) {
            $this->requestContentType = self::FORM_MULTIPART;
        } elseif ($isUrlencodedFormRequest) {
            $this->requestContentType = self::FORM_URLENCODED;
        } else {
            $this->requestContentType = $requestContentType;
        }
    }

    public function isJson(): bool
    {
        return $this->requestContentType === self::APPLICATION_JSON;
    }

    public function isXml(): bool
    {
        return $this->requestContentType === self::APPLICATION_XML;
    }

    public function isMultipartForm(): bool
    {
        return $this->requestContentType === self::FORM_MULTIPART;
    }

    public function isUrlencodedForm(): bool
    {
        return $this->requestContentType === self::FORM_URLENCODED;
    }

    public function isForm(): bool
    {
        return $this->isMultipartForm() || $this->isUrlencodedForm();
    }

    public function toString(): string
    {
        return $this->requestContentType;
    }
}