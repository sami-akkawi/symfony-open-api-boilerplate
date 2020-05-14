<?php declare(strict=1);

namespace App\ApiV1Bundle\Specification\Exception;

use LogicException;

final class SpecificException extends LogicException
{
    private static function generate(string $message): self
    {
        $backtrace = debug_backtrace();
        if (!empty($backtrace) && !empty($backtrace[3]) && is_array($backtrace[3])) {
            $message .= PHP_EOL . $backtrace[3]['file'] . ":" . $backtrace[3]['line'];
        }
        return new self($message, 500);
    }

    public static function generateInvalidEmailException(string $invalidEmail): self
    {
        return self::generate("Invalid Email: $invalidEmail.");
    }

    public static function generateInvalidUrlException(string $invalidUrl): self
    {
        return self::generate("Invalid URL: $invalidUrl.");
    }

    public static function generateEmptyStringException(string $field): self
    {
        return self::generate("$field cannot have an empty value.");
    }
}