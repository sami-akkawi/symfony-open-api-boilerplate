<?php declare(strict=1);

namespace App\ApiV1Bundle\Specification\Info\Contact;

use App\ApiV1Bundle\Specification\Exception\SpecificException;

final class ContactEmail
{
    private string $email;

    private function __construct(string $email)
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL, FILTER_FLAG_EMAIL_UNICODE)) {
            throw SpecificException::generateInvalidEmailException($email);
        }
        $this->email = $email;
    }

    public static function fromString(string $email): self
    {
        return new self($email);
    }

    public function toString(): string
    {
        return $this->email;
    }
}