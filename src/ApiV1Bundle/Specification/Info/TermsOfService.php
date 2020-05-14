<?php declare(strict=1);

namespace App\ApiV1Bundle\Specification\Info;

use App\ApiV1Bundle\Specification\Exception\SpecificException;

final class TermsOfService
{
    private string $termsOfService;

    private function __construct(string $termsOfService)
    {
        if (!filter_var($termsOfService, FILTER_VALIDATE_URL)) {
            throw SpecificException::generateInvalidUrlException($termsOfService);
        }
        $this->termsOfService = $termsOfService;
    }

    public static function fromString(string $termsOfService): self
    {
        return new self($termsOfService);
    }

    public function toString(): string
    {
        return $this->termsOfService;
    }
}