<?php declare(strict=1);

namespace App\ApiV1Bundle\ApiSpecification\ApiComponents\ResponseContent\MediaType;

final class MediaTypeMimeType
{
    const JSON         = 'application/json';
    const XML          = 'application/xml';

    private string $mimeType;

    private function __construct(string $mimeType)
    {
        $this->mimeType = $mimeType;
    }

    public static function generateJson(): self
    {
        return new self(self::JSON);
    }

    public function toString(): string
    {
        return $this->mimeType;
    }

    public function isIdenticalTo(self $mimeType): bool
    {
        return $this->toString() === $mimeType->toString();
    }
}