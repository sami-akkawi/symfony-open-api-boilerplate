<?php declare(strict_types=1);

namespace App\Message;

final class FieldMessage
{
    const PATH = 'path';
    const MESSAGE = 'message';

    private FieldMessagePath $path;
    private Message $message;

    public function __construct(FieldMessagePath $path, Message $message)
    {
        $this->path = $path;
        $this->message = $message;
    }

    public static function generate(array $path, Message $message): self
    {
        return new self(FieldMessagePath::fromArray($path), $message);
    }

    public function toArray(): array
    {
        return [
            self::PATH => $this->path->toArray(),
            self::MESSAGE => $this->message->toArray()
        ];
    }

    public function getDefaultText(): MessageDefaultText
    {
        return $this->message->getDefaultText();
    }

    public function prependPath($part): self
    {
        return new self($this->path->prepend($part), $this->message);
    }

    public function appendPath($part): self
    {
        return new self($this->path->append($part), $this->message);
    }

    public function getPath(): FieldMessagePath
    {
        return $this->path;
    }

    public function getMessage(): Message
    {
        return $this->message;
    }

    public static function fromArray(array $data): self
    {
        if (!in_array(self::PATH, $data)) {
            throw new \LogicException(self::PATH . ' is not defined in $data');
        }
        if (!in_array(self::MESSAGE, $data)) {
            throw new \LogicException(self::MESSAGE . ' is not defined in $data');
        }
        return new self(
            FieldMessagePath::fromArray($data[self::PATH]),
            Message::fromArray($data[self::MESSAGE])
        );
    }
}