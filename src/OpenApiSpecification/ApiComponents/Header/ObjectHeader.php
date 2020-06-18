<?php declare(strict_types=1);

namespace App\OpenApiSpecification\ApiComponents\Header;

use App\OpenApiSpecification\ApiComponents\Example;
use App\OpenApiSpecification\ApiComponents\Examples;
use App\OpenApiSpecification\ApiComponents\Schema\ObjectSchema;
use App\OpenApiSpecification\ApiComponents\Schemas;
use App\OpenApiSpecification\ApiException\SpecificationException;

final class ObjectHeader extends SchemaHeader
{
    public static function generate(): self
    {
        return new self(
            HeaderIsRequired::generateFalse(),
            HeaderIsDeprecated::generateFalse(),
            ObjectSchema::generate(Schemas::generate())
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

    public function setExample(Example $example): self
    {
        return new self(
            $this->isRequired,
            $this->isDeprecated,
            $this->schema->makeNullable(),
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
            $this->schema->makeNullable(),
            $this->description,
            $this->docName,
            null,
            $examples->addExample($example, $example->getName()->toString())
        );
    }
}