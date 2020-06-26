<?php declare(strict_types=1);

namespace App\OpenApiSpecification\ApiComponents\ComponentsMediaType;

final class MediaTypeMimeType
{
    const ANY          = '*/*';

    const JSON         = 'application/json';
    const OCTET_STREAM = 'application/octet-stream';
    const PDF          = 'application/pdf';
    const URL_ENCODED  = 'application/x-www-form-urlencoded';
    const XML          = 'application/xml';

    const TEXT         = 'text/*';
    const HTML_TEXT    = 'text/html';
    const PLAIN_TEXT   = 'text/plain; charset=utf-8';

    const IMAGE        = 'image/*';
    const GIF          = 'image/gif';
    const JPEG         = 'image/jpeg';
    const JPG          = 'image/jpg';
    const PNG          = 'image/png';

    const FORM_DATA    = 'multipart/form-data'; // to upload multiple files
    const MIXED        = 'multipart/mixed';

    private string $mimeType;

    private function __construct(string $mimeType)
    {
        $this->mimeType = $mimeType;
    }

    public static function generateJson(): self
    {
        return new self(self::JSON);
    }

    public static function generateXml(): self
    {
        return new self(self::XML);
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