<?php declare(strict_types=1);

namespace App\Message;

final class MessageType
{
    private const INFO = 'info';
    private const SUCCESS = 'success';
    private const WARNING = 'warning';
    private const ERROR = 'error';

    private const VALID_TYPES = [self::INFO, self::SUCCESS, self::WARNING, self::ERROR];

    private string $type;

    private function __construct(string $type)
    {
        if (!in_array($type, self::VALID_TYPES)) {
            throw new \LogicException('invalid message type');
        }

        $this->type = $type;
    }

    public static function generateInfo(): self
    {
        return new self(self::INFO);
    }

    public static function generateSuccess(): self
    {
        return new self(self::SUCCESS);
    }

    public static function generateWarning(): self
    {
        return new self(self::WARNING);
    }

    public static function generateError(): self
    {
        return new self(self::ERROR);
    }

    public static function fromString(string $type): self
    {
        return new self($type);
    }

    public function toString(): string
    {
        return $this->type;
    }

    public static function getValidTypes(): array
    {
        return self::VALID_TYPES;
    }
}