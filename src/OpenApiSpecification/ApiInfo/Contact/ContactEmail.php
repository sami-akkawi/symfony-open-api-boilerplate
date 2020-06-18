<?php declare(strict=1);

namespace App\OpenApiSpecification\ApiInfo\Contact;

use App\OpenApiSpecification\ApiException\SpecificationException;

/**
 * The email address of the contact person/organization. MUST be in the format of an email address.
 * http://spec.openapis.org/oas/v3.0.3#contact-object
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