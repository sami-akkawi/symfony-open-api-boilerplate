<?php declare(strict_types=1);

namespace App\ApiV1Bundle\Response;

use App\ApiV1Bundle\Schema\ErrorResponseSchema;
use App\Message\FieldMessage;
use App\Message\Message;
use App\OpenApiSpecification\ApiComponents\ComponentsResponse\ResponseSchema;

final class CorruptDataResponse extends AbstractErrorResponse
{
    public static function generate(array $headers): self
    {
        return new self([], [], $headers);
    }

    public function addMessage(Message $message): self
    {
        return new self(array_merge($this->messages, [$message]), $this->fieldMessages, $this->headers);
    }

    public function addFieldMessage(FieldMessage $fieldMessage): self
    {
        return new self($this->messages, array_merge($this->fieldMessages, [$fieldMessage]), $this->headers);
    }

    protected static function getOpenApiResponseWithoutName(): ResponseSchema
    {
        return ResponseSchema::generateCorruptDataResponse(
            ErrorResponseSchema::getReferenceSchema()
        );
    }
}