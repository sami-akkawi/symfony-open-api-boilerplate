<?php declare(strict_types=1);

namespace App\ApiV1Bundle\Response;

use App\Message\FieldMessage;
use App\Message\Message;

abstract class AbstractErrorResponse extends AbstractResponse
{
    /** @var Message[] */
    protected array $messages;
    /** @var FieldMessage[] */
    protected array $fieldMessages;

    protected function __construct(array $messages, array $fieldMessages)
    {
        $this->messages = $messages;
        $this->fieldMessages = $fieldMessages;
    }

    protected function toArray(): array
    {
        $messages = [];
        foreach ($this->messages as $fieldMessage) {
            $messages[] = $fieldMessage->toArray();
        }
        $fieldMessages = [];
        foreach ($this->fieldMessages as $fieldMessage) {
            $fieldMessages[] = $fieldMessage->toArray();
        }
        return [
            'errorType' => static::getOpenApiResponseWithoutName()->getDescription()->toString(),
            'messages' => $messages,
            'fieldMessages' => $fieldMessages
        ];
    }

    private function getErrorType(): string
    {
        $path = explode('\\', static::class);
        return array_pop($path);
    }
}