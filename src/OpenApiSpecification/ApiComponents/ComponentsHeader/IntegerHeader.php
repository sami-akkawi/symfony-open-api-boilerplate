<?php declare(strict_types=1);

namespace App\OpenApiSpecification\ApiComponents\ComponentsHeader;

use App\OpenApiSpecification\ApiComponents\ComponentsExample;
use App\OpenApiSpecification\ApiComponents\ComponentsExamples;
use App\OpenApiSpecification\ApiComponents\ComponentsSchema\IntegerSchema;
use App\OpenApiSpecification\ApiComponents\ComponentsHeader\Header\HeaderIsRequired;
use App\OpenApiSpecification\ApiComponents\ComponentsHeader\Header\HeaderIsDeprecated;
use App\OpenApiSpecification\ApiComponents\ComponentsHeader\Header\HeaderDescription;
use App\OpenApiSpecification\ApiComponents\ComponentsHeader\Header\HeaderKey;
use App\OpenApiSpecification\ApiException\SpecificationException;

final class IntegerHeader extends SchemaHeader
{
    public static function generate(): self
    {
        return new self(
            HeaderIsRequired::generateFalse(),
            HeaderIsDeprecated::generateFalse(),
            IntegerSchema::generate()
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

    public function setMinimum(int $minimum): self
    {
        return new self(
            $this->isRequired,
            $this->isDeprecated,
            $this->schema->setMinimum($minimum),
            $this->description,
            $this->key,
            $this->example,
            $this->examples
        );
    }

    public function setMaximum(int $maximum): self
    {
        return new self(
            $this->isRequired,
            $this->isDeprecated,
            $this->schema->setMaximum($maximum),
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

    public function setKey(string $key): self
    {
        return new self(
            $this->isRequired,
            $this->isDeprecated,
            $this->schema,
            $this->description,
            HeaderKey::fromString($key),
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
            $examples->addExample($example, $example->getName()->toString())
        );
    }
}