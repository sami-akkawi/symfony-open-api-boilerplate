<?php declare(strict_types=1);

namespace App\ApiV1Bundle\Response;

abstract class AbstractSuccessResponse extends AbstractResponse
{
    protected array $data;
    protected array $generalMessages;
    protected array $fieldMessages;
    protected bool $withData;

    protected function __construct(
        array $data,
        array $generalMessages,
        array $fieldMessages,
        bool $withData = true
    ) {
        $this->data = $data;
        $this->generalMessages = $generalMessages;
        $this->fieldMessages = $fieldMessages;
        $this->withData = $withData;
    }

    protected function toArray(): array
    {
        $response = [];
        if (!$this->withData) {
            return [];
        }

        $response['data'] = $this->data;

        if ($this->generalMessages) {
            $response['generalMessages'] = $this->generalMessages;
        }

        if ($this->fieldMessages) {
            $response['fieldMessages'] = $this->fieldMessages;
        }

        return $response;
    }
}