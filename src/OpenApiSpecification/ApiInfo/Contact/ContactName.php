<?php declare(strict_types=1);

namespace App\OpenApiSpecification\ApiInfo\Contact;

use App\OpenApiSpecification\ApiException\SpecificationException;

/**
 * The identifying name of the contact person/organization.
 * http://spec.openapis.org/oas/v3.0.3#contact-object
 */

final class ContactName
{
    private string $name;

    private function __construct(string $name)
    {
        if (empty($name)) {
            throw SpecificationException::generateEmptyStringException(self::class);
        }
        $this->name = $name;
    }

    public static function fromString(string $name): self
    {
        return new self($name);
    }

    public function toString(): string
    {
        return $this->name;
    }
}