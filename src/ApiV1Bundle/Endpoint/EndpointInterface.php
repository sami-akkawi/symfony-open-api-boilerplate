<?php declare(strict_types=1);

namespace App\ApiV1Bundle\Endpoint;

use App\ApiV1Bundle\Helpers\ApiV1Request;
use App\ApiV1Bundle\Response\AbstractResponse;
use App\OpenApiSpecification\ApiComponents\ComponentsParameters;
use App\OpenApiSpecification\ApiComponents\ComponentsRequestBody;
use App\OpenApiSpecification\ApiComponents\ComponentsResponses;
use App\OpenApiSpecification\ApiPath;
use App\OpenApiSpecification\ApiPath\PathOperation\OperationDescription;
use App\OpenApiSpecification\ApiPath\PathOperation\OperationName;
use App\OpenApiSpecification\ApiPath\PathOperation\OperationSummary;
use App\OpenApiSpecification\ApiPath\PathOperation\OperationTags;
use App\OpenApiSpecification\ApiPath\PathPartialUrl;

interface EndpointInterface
{
    public function handle(ApiV1Request $request): AbstractResponse;

    public static function isSecurityOptional(): bool;

    public static function getOperationName(): OperationName;

    public static function getPartialPath(): PathPartialUrl;

    public static function getTags(): OperationTags;

    public static function getRequestBody(): ?ComponentsRequestBody;

    public static function getParameters(): ComponentsParameters;

    public static function getAllResponses(): ComponentsResponses;

    public static function getDescription(): ?OperationDescription;

    public static function getSummary(): ?OperationSummary;

    public static function getApiPath(): ApiPath;
}