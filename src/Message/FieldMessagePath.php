<?php declare(strict_types=1);

namespace App\Message;

final class FieldMessagePath
{
    private array $path;

    private function __construct(array $path)
    {
        foreach ($path as $item) {
            if (!is_string($item) && !is_int($item)) {
                throw new \LogicException('a field message path can only have string and integer entries');
            }
        }
        $this->path = $path;
    }

    public static function fromArray(array $path): self
    {
        return new self($path);
    }

    public function toArray(): array
    {
        return $this->path;
    }

    public function append($part): self
    {
        $path = $this->path;
        array_push($path, $part);
        return new self($path);
    }

    public function prepend($part): self
    {
        $path = $this->path;
        array_unshift($path, $part);
        return new self($path);
    }
}