<?php declare(strict_types=1);

namespace App\ApiV1Bundle\Helpers;

final class ApiDependencies
{
    private Validator $formatValidator;

    public function __construct(Validator $formatValidator)
    {
        $this->formatValidator = $formatValidator;
    }

    public function getFormatValidator(): Validator
    {
        return $this->formatValidator;
    }
}