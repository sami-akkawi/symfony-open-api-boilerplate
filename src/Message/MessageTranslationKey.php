<?php declare(strict_types=1);

namespace App\Message;

final class MessageTranslationKey
{
    private string $key;

    private function __construct(string $key)
    {
        $this->key = $key;
    }

    public static function fromString(string $key): self
    {
        return new self($key);
    }

    public function toString(): string
    {
        return $this->key;
    }
}