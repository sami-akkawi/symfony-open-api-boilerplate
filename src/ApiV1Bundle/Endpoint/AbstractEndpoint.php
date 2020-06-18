<?php declare(strict_types=1);

namespace App\ApiV1Bundle\Endpoint;

use App\OpenApiSpecification\ApiComponents\Parameters;
use App\OpenApiSpecification\ApiComponents\RequestBody;
use App\OpenApiSpecification\ApiComponents\Responses;
use App\OpenApiSpecification\ApiInfo\Version;
use App\OpenApiSpecification\ApiPath;
use App\OpenApiSpecification\ApiPath\PathOperation;
use App\OpenApiSpecification\ApiPath\PathOperation\OperationDescription;
use App\OpenApiSpecification\ApiPath\PathOperation\OperationId;
use App\OpenApiSpecification\ApiPath\PathOperation\OperationName;
use App\OpenApiSpecification\ApiPath\PathOperation\OperationSummary;
use App\OpenApiSpecification\ApiPath\PathOperation\OperationTags;
use App\OpenApiSpecification\ApiPath\PathPartialUrl;

abstract class AbstractEndpoint
{
    public abstract static function getOperationName(): OperationName;

    public abstract static function getPartialPath(): PathPartialUrl;

    public static function getPath(): string
    {
        return '/' . Version::getMajorVersion() . static::getPartialPath()->toString();
    }

    public abstract static function getTags(): OperationTags;

    public static function getOperationId(): OperationId
    {
        return OperationId::fromString(static::class);
    }

    public static function isDeprecated(): bool
    {
        return false;
    }

    public static function getParameters(): Parameters
    {
        return Parameters::generate();
    }

    public abstract static function getResponses(): Responses;

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
            static::getResponses()
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