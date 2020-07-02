<?php declare(strict_types=1);

namespace App\ApiV1Bundle\Response;

use App\ApiV1Bundle\Helpers\JsonToXmlConverter;
use App\OpenApiSpecification\ApiComponents\ComponentsResponse\ReferenceResponse;
use App\OpenApiSpecification\ApiComponents\ComponentsResponse\ResponseSchema;
use Symfony\Component\HttpFoundation\Response;

abstract class AbstractResponse
{
    public static function getOpenApiResponse(): ResponseSchema
    {
        return static::getOpenApiResponseWithoutName()->setName(static::getClassName());
    }

    abstract protected static function getOpenApiResponseWithoutName(): ResponseSchema;

    public static function getReferenceResponse(): ReferenceResponse
    {
        return ReferenceResponse::generate(static::getClassName(), static::getOpenApiResponseWithoutName());
    }

    public function toJsonResponse(): Response
    {
        return new Response(json_encode($this->toArray(), JSON_PRETTY_PRINT), (int)self::getHttpCode());
    }

    public function toXmlResponse(): Response
    {
        return new Response(JsonToXmlConverter::convert(json_encode($this->toArray())), (int)self::getHttpCode());
    }

    private static function getHttpCode(): string
    {
        return static::getOpenApiResponseWithoutName()->getCode()->toString();
    }

    public static function getClassName(): string
    {
        $path = explode('\\', static::class);
        return array_pop($path);
    }

    abstract protected function toArray(): array;
}