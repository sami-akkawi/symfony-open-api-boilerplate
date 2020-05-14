<?php declare(strict=1);

namespace App\ApiV1Bundle\Specification\Info\License;

use App\ApiV1Bundle\Specification\Exception\SpecificException;

final class LicenseUrl
{
    private string $url;

    private function __construct(string $url)
    {
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            throw SpecificException::generateInvalidUrlException($url);
        }
        $this->url = $url;
    }

    public static function fromString(string $url): self
    {
        return new self($url);
    }

    public function toString(): string
    {
        return $this->url;
    }
}