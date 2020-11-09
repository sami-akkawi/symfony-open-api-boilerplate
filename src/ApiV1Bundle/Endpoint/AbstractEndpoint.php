<?php declare(strict_types=1);

namespace App\ApiV1Bundle\Endpoint;

use App\ApiV1Bundle\Helpers\ApiDependencies;
use App\ApiV1Bundle\Helpers\ApiV1Request;
use App\ApiV1Bundle\Helpers\FormatValidator;
use App\ApiV1Bundle\Helpers\RequestContentType;
use App\ApiV1Bundle\Helpers\ResponseContentType;
use App\ApiV1Bundle\Response\AbstractErrorResponse;
use App\ApiV1Bundle\Response\AbstractResponse;
use App\ApiV1Bundle\Response\CorruptDataResponse;
use App\ApiV1Bundle\Response\UnprocessableEntityResponse;
use App\Message\FieldMessage;
use App\OpenApiSpecification\ApiComponents\ComponentsParameters;
use App\OpenApiSpecification\ApiComponents\ComponentsRequestBody;
use App\OpenApiSpecification\ApiComponents\ComponentsResponses;
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
    abstract public static function getOperationName(): OperationName;

    abstract public static function getPartialPath(): PathPartialUrl;

    protected FormatValidator $validator;

    public function __construct(ApiDependencies $dependencies)
    {
        $this->validator = $dependencies->getFormatValidator();
    }

    public function handle(Request $request): Response
    {
        $requestContentType = new RequestContentType($request->headers->get('content-type'));
        $responseContentType = new ResponseContentType($request->headers->get('accept'));

        $headers = [
            'origin' => $request->headers->get('origin'),
            'content-type' => $responseContentType->mustBeXml() ? ResponseContentType::APPLICATION_XML : ResponseContentType::APPLICATION_JSON
        ];

        [$pathParams, $requestBody, $queryParams, $headerParams, $cookieParams] =
            $this->validator->getAndValidateParametersRequest($request, $this, $requestContentType);

        // todo: if endpoint needs authentication, check authentication
        //  return NotAuthenticatedResponse (401 Unauthorized)
        // todo: if endpoint has permissions or needs authorization, check permissions
        //  return NotAuthorizedResponse (403 Forbidden)

        if ($this->validator->hasErrors()) {
            $errorResponse = UnprocessableEntityResponse::generate();
            $errorResponse = $this->addErrorsMessagesToResponse($errorResponse);
            $response = $responseContentType->mustBeXml() ? $errorResponse->toXmlResponse() : $errorResponse->toJsonResponse();
            $response->headers->add($headers);
            return $response;
        }

        $endpointResponse = $this->subHandle(
            new ApiV1Request($request, $pathParams, $requestBody, $queryParams, $headerParams, $cookieParams)
        );

        // endpointResponse should return a response with http code:
        //  OkResponse (200 Ok)
        //  CreatedResponse (201 Created)
        //  AcceptedResponse (202 Accepted) (For requests with longer processing time, like background jobs / imports...)
        //  BadRequestResponse (400 Bad Request) (For Input Validation Errors)
        //  NotFoundResponse (404 Bad Request) (For e.g. query by id)

        $response = $responseContentType->mustBeXml() ? $endpointResponse->toXmlResponse() : $endpointResponse->toJsonResponse();
        $response->headers->add($headers);

        $this->validator->validateResponse($response, $this, $responseContentType->mustBeXml());
        if ($this->validator->hasErrors()) {
            $errorResponse = CorruptDataResponse::generate();
            $errorResponse = $this->addErrorsMessagesToResponse($errorResponse);
            $response = $responseContentType->mustBeXml() ? $errorResponse->toXmlResponse() : $errorResponse->toJsonResponse();
            $response->headers->add($headers);
            return $response;
        }

        return $response;
    }

    private function addErrorsMessagesToResponse(AbstractErrorResponse $errorResponse): AbstractErrorResponse
    {
        foreach ($this->validator->getErrors() as $message) {
            if ($message instanceof FieldMessage) {
                $errorResponse = $errorResponse->addFieldMessage($message);
                continue;
            }
            $errorResponse = $errorResponse->addMessage($message);
        }
        return $errorResponse;
    }

    abstract protected function subHandle(ApiV1Request $request): AbstractResponse;

    abstract public static function getTags(): OperationTags;

    public static function getOperationId(): OperationId
    {
        return OperationId::fromString(static::class);
    }

    public static function isDeprecated(): bool
    {
        return false;
    }

    public static function getAllResponses(): ComponentsResponses
    {
        return static::getResponses()
            ->addResponse(UnprocessableEntityResponse::getReferenceResponse())
            ->addResponse(CorruptDataResponse::getReferenceResponse());
    }

    abstract protected static function getResponses(): ComponentsResponses;

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

    public static function getRequestBody(): ?ComponentsRequestBody
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