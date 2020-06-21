<?php declare(strict_types=1);

namespace App\Message;

final class MessageDefaultText
{
    private string $defaultText;

    private function __construct(string $defaultText)
    {
        $this->defaultText = $defaultText;
    }

    public static function fromString(string $defaultText): self
    {
        return new self($defaultText);
    }

    public function toString(): string
    {
        return $this->defaultText;
    }
}