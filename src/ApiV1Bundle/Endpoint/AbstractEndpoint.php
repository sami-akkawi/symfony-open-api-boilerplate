<?php declare(strict_types=1);

namespace App\ApiV1Bundle\Endpoint;

use App\ApiV1Bundle\Helpers\ApiDependencies;
use App\ApiV1Bundle\Helpers\FormatValidator;
use App\ApiV1Bundle\Helpers\JsonToXmlConverter;
use App\ApiV1Bundle\Response\CorruptDataResponse;
use App\ApiV1Bundle\Response\UnprocessableEntityResponse;
use App\Message\FieldMessage;
use App\OpenApiSpecification\ApiComponents\ComponentsParameters;
use App\OpenApiSpecification\ApiComponents\RequestBody;
use App\OpenApiSpecification\ApiComponents\Responses;
use App\OpenApiSpecification\ApiPath;
use App\OpenApiSpecification\ApiPath\PathOperation;
use App\OpenApiSpecification\ApiPath\PathOperation\OperationDescription;
use App\OpenApiSpecification\ApiPath\PathOperation\OperationId;
use App\OpenApiSpecification\ApiPath\PathOperation\OperationName;
use App\OpenApiSpecification\ApiPath\PathOperation\OperationSummary;
use App\OpenApiSpecification\ApiPath\PathOperation\OperationTags;
use App\OpenApiSpecification\ApiPath\PathPartialUrl;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

abstract class AbstractEndpoint
{
    public abstract static function getOperationName(): OperationName;

    public abstract static function getPartialPath(): PathPartialUrl;

    protected FormatValidator $validator;

    public function __construct(ApiDependencies $dependencies)
    {
        $this->validator = $dependencies->getFormatValidator();
    }

    public function handle(Request $request): Response
    {
        $contentType = $request->headers->get('content-type');
        if (empty($contentType)) {
            $contentType = 'application/json';
        }

        $headers = [
            'accept' => $request->headers->get('accept'),
            'content-type' => $contentType,
            'origin' => $request->headers->get('origin')
        ];

        $isXml = $headers['content-type'] === 'application/xml';

        [$pathParams, $requestBody, $queryParams, $headerParams, $cookieParams] =
            $this->validator->getAndValidateParametersRequest($request, $this);

        // todo: if endpoint needs authentication, check authentication
        //  return NotAuthenticatedResponse (401 Unauthorized)
        // todo: if endpoint has permissions or needs authorization, check permissions
        //  return NotAuthorizedResponse (403 Forbidden)

        if ($this->validator->hasErrors()) {
            $response = UnprocessableEntityResponse::generate($headers);
            foreach ($this->validator->getErrors() as $message) {
                if ($message instanceof FieldMessage) {
                    $response = $response->addFieldMessage($message);
                    continue;
                }
                $response = $response->addMessage($message);
            }
            return $isXml ? $response->toXmlResponse() : $response->toJsonResponse();
        }

        $endpointResponse = $this->subHandle($pathParams, $requestBody, $queryParams, $headerParams, $cookieParams);

        // endpointResponse should return a response with http code:
        //  OkResponse (200 Ok)
        //  CreatedResponse (201 Created)
        //  AcceptedResponse (202 Accepted) (For requests with longer processing time, like background jobs / imports...)
        //  BadRequestResponse (400 Bad Request) (For Input Validation Errors)
        //  NotFoundResponse (404 Bad Request) (For e.g. query by id)

        $endpointResponse->headers->add($headers);

        $this->validator->validateResponse($endpointResponse, $this);
        if ($this->validator->hasErrors()) {
            $response = CorruptDataResponse::generate($headers);
            foreach ($this->validator->getErrors() as $message) {
                if ($message instanceof FieldMessage) {
                    $response = $response->addFieldMessage($message);
                    continue;
                }
                $response = $response->addMessage($message);
            }
            return $isXml ? $response->toXmlResponse() : $response->toJsonResponse();
        }

        if ($isXml) {
            return new Response(
                JsonToXmlConverter::convert($endpointResponse->getContent()),
                $endpointResponse->getStatusCode(),
                $endpointResponse->headers->all()
            );
        }

        return $endpointResponse;
    }

    protected abstract function subHandle(
        array $pathParams,
        array $requestBody,
        array $queryParams,
        array $headerParams,
        array $cookieParams
    ): Response;

    public abstract static function getTags(): OperationTags;

    public static function getOperationId(): OperationId
    {
        return OperationId::fromString(static::class);
    }

    public static function isDeprecated(): bool
    {
        return false;
    }

    public static function getAllResponses(): Responses
    {
        return static::getResponses()
            ->addResponse(UnprocessableEntityResponse::getReferenceResponse())
            ->addResponse(CorruptDataResponse::getReferenceResponse());
    }

    protected abstract static function getResponses(): Responses;

    public static function getParameters(): ComponentsParameters
    {
        return ComponentsParameters::generate();
    }

    public static function getDescription(): ?OperationDescription
    {
        return null;
    }

    public static function getSummary(): ?OperationSummary
    {
        return null;
    }

    public static function getRequestBody(): ?RequestBody
    {
        return null;
    }

    public static function isSecurityOptional(): bool
    {
        return false;
    }

    public static function getApiPath(): ApiPath
    {
        $description = static::getDescription();
        $summary = static::getSummary();
        $requestBody = static::getRequestBody();
        $operation = PathOperation::generate(
            static::getOperationName(),
            static::getOperationId(),
            static::getParameters(),
            static::getAllResponses()
        )
            ->addTags(static::getTags());

        if ($requestBody) {
            $operation = $operation->setRequestBody($requestBody);
        }


        if ($description) {
            $operation = $operation->setDescription($description);
        }

        if ($summary) {
            $operation = $operation->setSummary($summary);
        }

        if (static::isDeprecated()) {
            $operation = $operation->deprecate();
        }

        if (static::isSecurityOptional()) {
            $operation = $operation->setSecurityOptional();
        }
        return ApiPath::generate(static::getPartialPath())
            ->addOperation($operation);
    }
}