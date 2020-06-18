<?php declare(strict_types=1);

namespace App\OpenApiSpecification\ApiComponents\Header;

use App\OpenApiSpecification\ApiComponents\Example;
use App\OpenApiSpecification\ApiComponents\Examples;
use App\OpenApiSpecification\ApiComponents\Schema\StringSchema;
use App\OpenApiSpecification\ApiException\SpecificationException;

final class StringHeader extends SchemaHeader
{
    public static function generate(): self
    {
        return new self(
            HeaderIsRequired::generateFalse(),
            HeaderIsDeprecated::generateFalse(),
            StringSchema::generate()
        );
    }

    public function makeNullable(): self
    {
        return new self(
            $this->isRequired,
            $this->isDeprecated,
            $this->schema->makeNullable(),
            $this->description,
            $this->docName,
            $this->example,
            $this->examples
        );
    }

    public function require(): self
    {
        return new self(
            HeaderIsRequired::generateTrue(),
            $this->isDeprecated,
            $this->schema,
            $this->description,
            $this->docName,
            $this->example,
            $this->examples
        );
    }

    public function deprecate(): self
    {
        return new self(
            $this->isRequired,
            HeaderIsDeprecated::generateTrue(),
            $this->schema,
            $this->description,
            $this->docName,
            $this->example,
            $this->examples
        );
    }

    public function setFormat(string $format): self
    {
        return new self(
            $this->isRequired,
            $this->isDeprecated,
            $this->schema->setFormat($format),
            $this->description,
            $this->docName,
            $this->example,
            $this->examples
        );
    }

    /** @param string[] $options */
    public function setOptions(array $options): self
    {
        return new self(
            $this->isRequired,
            $this->isDeprecated,
            $this->schema->setOptions($options),
            $this->description,
            $this->docName,
            $this->example,
            $this->examples
        );
    }

    public function setMinimumLength(int $minLength): self
    {
        return new self(
            $this->isRequired,
            $this->isDeprecated,
            $this->schema->setMinimumLength($minLength),
            $this->description,
            $this->docName,
            $this->example,
            $this->examples
        );
    }

    public function setMaximumLength(int $maxLength): self
    {
        return new self(
            $this->isRequired,
            $this->isDeprecated,
            $this->schema->setMaximumLength($maxLength),
            $this->description,
            $this->docName,
            $this->example,
            $this->examples
        );
    }

    public function setDescription(string $description): self
    {
        return new self(
            $this->isRequired,
            $this->isDeprecated,
            $this->schema,
            HeaderDescription::fromString($description),
            $this->docName,
            $this->example,
            $this->examples
        );
    }

    public function setDocName(string $name): self
    {
        return new self(
            $this->isRequired,
            $this->isDeprecated,
            $this->schema,
            $this->description,
            HeaderDocName::fromString($name),
            $this->example,
            $this->examples
        );
    }

    public function setExample(Example $example): self
    {
        return new self(
            $this->isRequired,
            $this->isDeprecated,
            $this->schema,
            $this->description,
            $this->docName,
            $example,
            null
        );
    }

    public function addExample(Example $example): self
    {
        if (!$example->hasName()) {
            throw SpecificationException::generateMustHaveKeyInComponents();
        }

        $examples = $this->examples;
        if (!$examples) {
            $examples = Examples::generate();
        }

        return new self(
            $this->isRequired,
            $this->isDeprecated,
            $this->schema,
            $this->description,
            $this->docName,
            null,
            $examples
        );
    }
}