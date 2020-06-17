<?php declare(strict=1);

namespace App\ApiV1Bundle\ApiSpecification\ApiComponents\Header;

use App\ApiV1Bundle\ApiSpecification\ApiComponents\Example;
use App\ApiV1Bundle\ApiSpecification\ApiComponents\Examples;
use App\ApiV1Bundle\ApiSpecification\ApiComponents\Schema\NumberSchema;
use App\ApiV1Bundle\ApiSpecification\ApiException\SpecificationException;

final class NumberHeader extends SchemaHeader
{
    public static function generate(): self
    {
        return new self(
            HeaderIsRequired::generateFalse(),
            HeaderIsDeprecated::generateFalse(),
            NumberSchema::generate()
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
            $examples->addExample($example, $example->getName()->toString())
        );
    }
}