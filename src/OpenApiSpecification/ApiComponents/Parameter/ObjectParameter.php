<?php declare(strict=1);

namespace App\OpenApiSpecification\ApiComponents\Parameter;

use App\OpenApiSpecification\ApiComponents\Example;
use App\OpenApiSpecification\ApiComponents\Examples;
use App\OpenApiSpecification\ApiComponents\Schema\ObjectSchema;
use App\OpenApiSpecification\ApiComponents\Schemas;
use App\OpenApiSpecification\ApiException\SpecificationException;

final class ObjectParameter extends SchemaParameter
{
    private static function generate(string $name, ParameterLocation $location): self
    {
        if ($location->isInPath()) {
            throw SpecificationException::generateCannotBeInPath('ObjectParameter');
        }

        return new self(
            ParameterName::fromString($name),
            $location,
            ParameterIsRequired::generateFalse(),
            ParameterIsDeprecated::generateFalse(),
            ObjectSchema::generate(Schemas::generate())
        );
    }

    public function styleAsMatrix(): self
    {
        if (!$this->location->isInPath()) {
            throw SpecificationException::generateStyleNotSupportedForLocation(
                ParameterStyle::MATRIX,
                $this->location->toString()
            );
        }

        return new self(
            $this->name,
            $this->location,
            $this->isRequired,
            $this->isDeprecated,
            $this->schema,
            $this->description,
            $this->docName,
            ParameterStyle::generateMatrix(),
            $this->example,
            $this->examples
        );
    }

    public function styleAsLabel(): self
    {
        if (!$this->location->isInPath()) {
            throw SpecificationException::generateStyleNotSupportedForLocation(
                ParameterStyle::LABEL,
                $this->location->toString()
            );
        }

        return new self(
            $this->name,
            $this->location,
            $this->isRequired,
            $this->isDeprecated,
            $this->schema,
            $this->description,
            $this->docName,
            ParameterStyle::generateLabel(),
            $this->example,
            $this->examples
        );
    }

    public function styleAsForm(): self
    {
        if (!$this->location->isInQuery() && !$this->location->isInCookie()) {
            throw SpecificationException::generateStyleNotSupportedForLocation(
                ParameterStyle::FORM,
                $this->location->toString()
            );
        }

        return new self(
            $this->name,
            $this->location,
            $this->isRequired,
            $this->isDeprecated,
            $this->schema,
            $this->description,
            $this->docName,
            ParameterStyle::generateForm(),
            $this->example,
            $this->examples
        );
    }

    public function styleAsSimple(): self
    {
        throw SpecificationException::generateStyleNotSupportedForType(
            ParameterStyle::SIMPLE,
            $this->getParameterType()
        );
    }

    public function styleAsSpaceDelimited(): self
    {
        throw SpecificationException::generateStyleNotSupportedForType(
            ParameterStyle::SPACE_DELIMITED,
            $this->getParameterType()
        );
    }

    public function styleAsPipeDelimited(): self
    {
        throw SpecificationException::generateStyleNotSupportedForType(
            ParameterStyle::PIPE_DELIMITED,
            $this->getParameterType()
        );
    }

    public function styleAsDeepObject(): self
    {
        if (!$this->location->isInQuery()) {
            throw SpecificationException::generateStyleNotSupportedForLocation(
                ParameterStyle::DEEP_OBJECT,
                $this->location->toString()
            );
        }

        return new self(
            $this->name,
            $this->location,
            $this->isRequired,
            $this->isDeprecated,
            $this->schema,
            $this->description,
            $this->docName,
            ParameterStyle::generateDeepObject(),
            $this->example,
            $this->examples
        );
    }

    public static function generateInQuery(string $name): self
    {
        return self::generate($name, ParameterLocation::generateQuery());
    }

    public static function generateInHeader(string $name): self
    {
        return self::generate($name, ParameterLocation::generateHeader());
    }

    public static function generateInPath(string $name): self
    {
        throw SpecificationException::generateCannotBeInPath('ObjectParameter');
    }

    public static function generateInCookie(string $name): self
    {
        return self::generate($name, ParameterLocation::generateCookie());
    }

    public function require(): self
    {
        return new self(
            $this->name,
            $this->location,
            ParameterIsRequired::generateTrue(),
            $this->isDeprecated,
            $this->schema,
            $this->description,
            $this->docName,
            $this->style,
            $this->example,
            $this->examples
        );
    }

    public function deprecate(): self
    {
        return new self(
            $this->name,
            $this->location,
            $this->isRequired,
            ParameterIsDeprecated::generateTrue(),
            $this->schema,
            $this->description,
            $this->docName,
            $this->style,
            $this->example,
            $this->examples
        );
    }

    public function setDescription(string $description): self
    {
        return new self(
            $this->name,
            $this->location,
            $this->isRequired,
            $this->isDeprecated,
            $this->schema,
            ParameterDescription::fromString($description),
            $this->docName,
            $this->style,
            $this->example,
            $this->examples
        );
    }

    public function setDocName(string $name): self
    {
        return new self(
            $this->name,
            $this->location,
            $this->isRequired,
            $this->isDeprecated,
            $this->schema,
            $this->description,
            ParameterDocName::fromString($name),
            $this->style,
            $this->example,
            $this->examples
        );
    }

    public function makeNullable(): self
    {
        return new self(
            $this->name,
            $this->location,
            $this->isRequired,
            $this->isDeprecated,
            $this->schema->makeNullable(),
            $this->description,
            $this->docName,
            $this->style,
            $this->example,
            $this->examples
        );
    }

    public function setExample(Example $example): self
    {
        return new self(
            $this->name,
            $this->location,
            $this->isRequired,
            $this->isDeprecated,
            $this->schema->makeNullable(),
            $this->description,
            $this->docName,
            $this->style,
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
            $this->name,
            $this->location,
            $this->isRequired,
            $this->isDeprecated,
            $this->schema->makeNullable(),
            $this->description,
            $this->docName,
            $this->style,
            null,
            $examples->addExample($example, $example->getName()->toString())
        );
    }
}