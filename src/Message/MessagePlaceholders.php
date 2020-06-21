<?php declare(strict_types=1);

namespace App\Message;

final class MessagePlaceholders
{
    private array $placeholders;

    private function __construct(array $placeholders)
    {
        $this->placeholders = $placeholders;
    }

    public static function fromArray(array $placeholders): self
    {
        return new self($placeholders);
    }

    public function toArray(): array
    {
        return $this->placeholders;
    }
}