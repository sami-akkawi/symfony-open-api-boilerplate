<?php declare(strict_types=1);

namespace App\ApiV1Bundle\Response;

use App\ApiV1Bundle\Schema\ErrorResponseSchema;
use App\Message\FieldMessage;
use App\Message\Message;
use App\OpenApiSpecification\ApiComponents\ComponentsResponse\ResponseSchema;

final class UnprocessableEntityResponse extends AbstractErrorResponse
{
    public static function generate(): self
    {
        return new self([], []);
    }

    public function addMessage(Message $message): self
    {
        return new self(array_merge($this->messages, [$message]), $this->fieldMessages);
    }

    public function addFieldMessage(FieldMessage $fieldMessage): self
    {
        return new self($this->messages, array_merge($this->fieldMessages, [$fieldMessage]));
    }

    protected static function getOpenApiResponseWithoutName(): ResponseSchema
    {
        return ResponseSchema::generateUnprocessableEntityResponse(
            ErrorResponseSchema::getReferenceSchema()
        );
    }
}