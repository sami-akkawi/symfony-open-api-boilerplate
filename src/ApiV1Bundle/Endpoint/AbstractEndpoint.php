<?php declare(strict=1);

namespace App\ApiV1Bundle\Endpoint;

use App\ApiV1Bundle\ApiSpecification\ApiComponents\Parameters;
use App\ApiV1Bundle\ApiSpecification\ApiComponents\RequestBody;
use App\ApiV1Bundle\ApiSpecification\ApiComponents\Responses;
use App\ApiV1Bundle\ApiSpecification\ApiInfo\Version;
use App\ApiV1Bundle\ApiSpecification\ApiPath;
use App\ApiV1Bundle\ApiSpecification\ApiPath\PathOperation;
use App\ApiV1Bundle\ApiSpecification\ApiPath\PathOperation\OperationDescription;
use App\ApiV1Bundle\ApiSpecification\ApiPath\PathOperation\OperationId;
use App\ApiV1Bundle\ApiSpecification\ApiPath\PathOperation\OperationName;
use App\ApiV1Bundle\ApiSpecification\ApiPath\PathOperation\OperationSummary;
use App\ApiV1Bundle\ApiSpecification\ApiPath\PathOperation\OperationTags;
use App\ApiV1Bundle\ApiSpecification\ApiPath\PathPartialUrl;

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