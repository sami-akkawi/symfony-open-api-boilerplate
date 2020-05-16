<?php declare(strict=1);

namespace App\ApiV1Bundle\Specification\Info\Contact;

use App\ApiV1Bundle\Specification\Exception\SpecificationException;

/**
 * The email address of the contact person/organization. MUST be in the format of an email address.
 * https://swagger.io/specification/#contact-object
 */

final class ContactEmail
{
    private string $email;

    private function __construct(string $email)
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL, FILTER_FLAG_EMAIL_UNICODE)) {
            throw SpecificationException::generateInvalidEmailException($email);
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