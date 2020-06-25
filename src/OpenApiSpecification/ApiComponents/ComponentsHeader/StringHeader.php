<?php declare(strict_types=1);

namespace App\OpenApiSpecification\ApiComponents\ComponentsHeader;

use App\OpenApiSpecification\ApiComponents\ComponentsExample;
use App\OpenApiSpecification\ApiComponents\ComponentsExamples;
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
            $this->key,
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
            $this->key,
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
            $this->key,
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
            $this->key,
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
            $this->key,
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
            $this->key,
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
            $this->key,
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
            $this->key,
            $this->example,
            $this->examples
        );
    }

    public function setKey(string $name): self
    {
        return new self(
            $this->isRequired,
            $this->isDeprecated,
            $this->schema,
            $this->description,
            HeaderKey::fromString($name),
            $this->example,
            $this->examples
        );
    }

    public function setExample(ComponentsExample $example): self
    {
        return new self(
            $this->isRequired,
            $this->isDeprecated,
            $this->schema,
            $this->description,
            $this->key,
            $example,
            null
        );
    }

    public function addExample(ComponentsExample $example): self
    {
        if (!$example->hasName()) {
            throw SpecificationException::generateMustHaveKeyInComponents();
        }

        $examples = $this->examples;
        if (!$examples) {
            $examples = ComponentsExamples::generate();
        }

        return new self(
            $this->isRequired,
            $this->isDeprecated,
            $this->schema,
            $this->description,
            $this->key,
            null,
            $examples
        );
    }
}