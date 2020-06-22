<?php declare(strict_types=1);

namespace App\ApiV1Bundle\Helpers;

final class ApiDependencies
{
    private FormatValidator $formatValidator;

    public function __construct(FormatValidator $formatValidator)
    {
        $this->formatValidator = $formatValidator;
    }

    public function getFormatValidator(): FormatValidator
    {
        return $this->formatValidator;
    }
}