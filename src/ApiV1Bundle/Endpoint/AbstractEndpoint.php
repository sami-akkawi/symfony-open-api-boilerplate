<?php declare(strict_types=1);

namespace App\ApiV1Bundle\Endpoint;

use App\ApiV1Bundle\Response\CorruptDataResponse;
use App\ApiV1Bundle\Response\UnprocessableEntityResponse;
use App\OpenApiSpecification\ApiComponents\ComponentsParameters;
use App\OpenApiSpecification\ApiComponents\ComponentsRequestBody;
use App\OpenApiSpecification\ApiComponents\ComponentsResponses;
use App\OpenApiSpecification\ApiPath;
use App\OpenApiSpecification\ApiPath\PathOperation;
use App\OpenApiSpecification\ApiPath\PathOperation\OperationDescription;
use App\OpenApiSpecification\ApiPath\PathOperation\OperationId;
use App\OpenApiSpecification\ApiPath\PathOperation\OperationSummary;

abstract class AbstractEndpoint implements EndpointInterface
{
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