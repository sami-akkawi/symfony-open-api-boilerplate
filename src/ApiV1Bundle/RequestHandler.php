<?php declare(strict_types=1);

namespace App\ApiV1Bundle;

use App\ApiV1Bundle\Endpoint\EndpointInterface;
use App\ApiV1Bundle\Helpers\ApiDependencies;
use App\ApiV1Bundle\Helpers\ApiV1Request;
use App\ApiV1Bundle\Helpers\RequestContentType;
use App\ApiV1Bundle\Helpers\ResponseContentType;
use App\ApiV1Bundle\Helpers\Validator;
use App\ApiV1Bundle\Response\AbstractErrorResponse;
use App\ApiV1Bundle\Response\AbstractResponse;
use App\ApiV1Bundle\Response\CorruptDataResponse;
use App\ApiV1Bundle\Response\UnprocessableEntityResponse;
use App\Message\FieldMessage;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

final class RequestHandler
{
    protected Validator $validator;

    public function __construct(ApiDependencies $dependencies)
    {
        $this->validator = $dependencies->getFormatValidator();
    }

    public function handle(SymfonyRequest $request, EndpointInterface $endpoint): SymfonyResponse
    {
        $appResponse = $this->getAppResponse($request, $endpoint);
        return $this->getSymfonyResponse($endpoint, $appResponse);
    }

    private function getAppResponse(SymfonyRequest $request, EndpointInterface $endpoint): AbstractResponse
    {
        $requestContentType = new RequestContentType($request->headers->get('content-type'));

        [$pathParams, $requestBody, $queryParams, $headerParams, $cookieParams] =
            $this->validator->getAndValidateParametersRequest($request, $endpoint, $requestContentType);

        // todo: if endpoint needs authentication, check authentication
        //  return NotAuthenticatedResponse (401 Unauthorized)
        // todo: if endpoint has permissions or needs authorization, check permissions
        //  return NotAuthorizedResponse (403 Forbidden)

        if ($this->validator->hasErrors()) {
            $errorResponse = UnprocessableEntityResponse::generate();
            $errorResponse = $this->addErrorsMessagesToResponse($errorResponse);
            return $errorResponse;
        }

        // endpointResponse should return a response with http code:
        //  OkResponse (200 Ok)
        //  CreatedResponse (201 Created)
        //  AcceptedResponse (202 Accepted) (For requests with longer processing time, like background jobs / imports...)
        //  BadRequestResponse (400 Bad Request) (For Input Validation Errors)
        //  NotFoundResponse (404 Bad Request) (For e.g. query by id)

        return $endpoint->handle(
            new ApiV1Request($request, $pathParams, $requestBody, $queryParams, $headerParams, $cookieParams)
        );
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

    private function getSymfonyResponse(
        EndpointInterface $endpoint,
        AbstractResponse $appResponse
    ): SymfonyResponse {
        $response = $appResponse->toJsonResponse();

        $this->validator->validateResponse($response, $endpoint);
        if ($this->validator->hasErrors()) {
            $errorResponse = CorruptDataResponse::generate();
            $errorResponse = $this->addErrorsMessagesToResponse($errorResponse);
            $response = $errorResponse->toJsonResponse();
        }

        return $response;
    }
}