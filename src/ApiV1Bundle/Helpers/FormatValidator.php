<?php declare(strict_types=1);

namespace App\ApiV1Bundle\Helpers;

use App\ApiV1Bundle\Endpoint\AbstractEndpoint;
use App\Message\Message;
use App\OpenApiSpecification\ApiComponents\ComponentsParameters;
use App\OpenApiSpecification\ApiComponents\ComponentsRequestBody;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class FormatValidator
{
    private array $errors;

    public function __construct()
    {
        $this->errors = [];
    }

    public function hasErrors(): bool
    {
        return count($this->errors) > 0;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    public function getAndValidateParametersRequest(Request $request, AbstractEndpoint $endpoint): array
    {
        $this->errors = [];

        $pathParams = $request->attributes->get('_route_params');

        $requestBody = [];
        $requestBodySchema = $endpoint::getRequestBody();
        if ($requestBodySchema) {
            $requestBody = $this->getRequestBody($requestBodySchema, $request);
        }
        $endpointParameters = $endpoint::getParameters();
        $queryParams = $this->getQueryParameters($endpointParameters, $request);
        $headerParams = $this->getHeaderParameters($endpointParameters, $request);
        $cookieParams = $this->getCookieParameters($endpointParameters, $request);

        return [
            $pathParams, $requestBody, $queryParams, $headerParams, $cookieParams
        ];
    }

    private function getQueryParameters(ComponentsParameters $parameters, Request $request): array
    {
        $queryBag = $request->query->all();
        $queryParameterSchemas = $parameters->getAllQueryParameters();
        foreach ($queryParameterSchemas as $queryParameter) {
            $name = $queryParameter->getName()->toString();
            if (!isset($queryBag[$name])) {
                if ($queryParameter->isRequired()) {
                    $this->errors[] = Message::generateError(
                        'query_parameter_is_required',
                        "$name is a required query parameter",
                        ['%parameterName%' => $name]
                    );
                }
                continue;
            }

            $typeValue = $queryParameter->getSchema()->getValueFromCastedString($queryBag[$name]);
            $this->mergeErrorMessages($queryParameter->isValueValid($typeValue));
            $queryBag[$name] = $typeValue;
        }
        return $queryBag;
    }

    private function getHeaderParameters(ComponentsParameters $parameters, Request $request): array
    {
        $headersBag = $request->headers->all();
        $headerParameterSchemas = $parameters->getAllHeaderParameters();
        foreach ($headerParameterSchemas as $headerParameter) {
            $name = $headerParameter->getName()->toString();
            if (!isset($headersBag[$name])) {
                if ($headerParameter->isRequired()) {
                    $this->errors[] = Message::generateError(
                        'header_parameter_is_required',
                        "$name is a required header parameter",
                        ['%parameterName%' => $name]
                    );
                }
                continue;
            }

            $typeValue = $headerParameter->getSchema()->getValueFromCastedString($headersBag[$name]);
            $this->mergeErrorMessages($headerParameter->isValueValid($typeValue));
            $headersBag[$name] = $typeValue;
        }
        return $headersBag;
    }

    private function getCookieParameters(ComponentsParameters $parameters, Request $request): array
    {
        $cookiesBag = $request->cookies->all();
        $cookieParameterSchemas = $parameters->getAllCookieParameters();
        foreach ($cookieParameterSchemas as $cookieParameter) {
            $name = $cookieParameter->getName()->toString();
            if (!isset($cookiesBag[$name])) {
                if ($cookieParameter->isRequired()) {
                    $this->errors[] = Message::generateError(
                        'cookie_parameter_is_required',
                        "$name is a required cookie parameter",
                        ['%parameterName%' => $name]
                    );
                }
                continue;
            }

            $typeValue = $cookieParameter->getSchema()->getValueFromCastedString($cookiesBag[$name]);
            $this->mergeErrorMessages($cookieParameter->isValueValid($typeValue));
            $cookiesBag[$name] = $typeValue;
        }
        return $cookiesBag;
    }

    private function mergeErrorMessages(array $errors): void
    {
        if (empty($errors)) {
            return;
        }
        foreach ($errors as $error) {
            $this->errors[] = $error;
        }

    }

    private function getRequestBody(ComponentsRequestBody $requestBodySchema, Request $request): array
    {
        $requestContentType = $request->headers->get('content-type');
        $schemaContentTypes = $requestBodySchema->getDefinedMimeTypes();

        $isXml = $requestContentType === 'application/xml';
        $isJson = $requestContentType === 'application/json';
        
        $requestBody = [];

        if ($isXml) {
            $doc = @simplexml_load_string($request->getContent());
            if ($doc === false) {
                $message = Message::generateError(
                    'invalid_xml',
                    "The XML request is invalid, please validate your data."
                );
                $this->errors[] = $message;
                return [];
            }
            $requestBody = json_decode(json_encode($doc), true);
        }

        if ($isJson) {
            $requestBody = json_decode($request->getContent(), true);
            if ($requestBody === null) {
                $message = Message::generateError(
                    'invalid_json',
                    "The JSON request is invalid, please validate your data."
                );
                $this->errors[] = $message;
                return [];
            }
        }

        if (!in_array($requestContentType, $schemaContentTypes)) {
            $message = Message::generateError(
                'mime_not_supported_for_request_body',
                "$requestContentType not supported in request body",
                ['%submittedMimeType%' => $requestContentType]
            );
            $this->errors[] = $message;
            return $requestBody;
        }

        if ($isJson) {
            $this->errors = $requestBodySchema->isValueValidByMimeType($requestContentType, $requestBody);
            return $requestBody;
        }

        if ($isXml) {
            $schema = $requestBodySchema->toRequestBody()->getSchemaByMimeType('application/xml');
            $cleanRequestBody = $schema->getValueFromCastedString(json_encode($requestBody));

            $this->errors = $requestBodySchema->isValueValidByMimeType($requestContentType, $cleanRequestBody);

            return $cleanRequestBody;
        }

        throw new \LogicException("Missing Handling of Request of Content Type $requestContentType");
    }


    public function validateResponse(Response $response, AbstractEndpoint $endpoint): void
    {
        $this->errors = [];

        $httpCode = (string)$response->getStatusCode();
        $endpointResponses = $endpoint::getAllResponses();

        $endpointResponse = $endpointResponses->getResponseByHttpCode((string)$httpCode);
        if (!$endpointResponse) {
            $message = Message::generateError(
                'response_http_code_not_defined',
                "$httpCode not defined for endpoint",
                ['%httpCode%' => (string)$httpCode]
            );
            $this->errors[] = $message;
            return;
        }

        $responseContentType = $response->headers->get('content-type');
        $schemaContentTypes = $endpointResponse->getDefinedMimeTypes();
        if (!in_array($responseContentType, $schemaContentTypes)) {
            $method = strtoupper($endpoint::getOperationName()->toString());
            $partialPath = $endpoint::getPartialPath()->toString();
            $message = Message::generateError(
                'mime_not_supported_for_response',
                "$responseContentType not defined for $method: $partialPath",
                [
                    '%submittedMimeType%' => $responseContentType,
                    '%method%' => $method,
                    '%partialPath%' => $partialPath
                ]
            );
            $this->errors[] = $message;
            return;
        }

        $this->errors = $endpointResponse->isValueValidByMimeType($responseContentType, json_decode($response->getContent(), true));
    }
}