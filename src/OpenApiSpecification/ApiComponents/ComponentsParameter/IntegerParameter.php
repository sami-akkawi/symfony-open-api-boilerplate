<?php declare(strict_types=1);

namespace App\OpenApiSpecification\ApiComponents\ComponentsParameter;

use App\OpenApiSpecification\ApiComponents\ComponentsExample;
use App\OpenApiSpecification\ApiComponents\ComponentsExamples;
use App\OpenApiSpecification\ApiComponents\ComponentsParameter\Parameter\ParameterDescription;
use App\OpenApiSpecification\ApiComponents\ComponentsParameter\Parameter\ParameterIsDeprecated;
use App\OpenApiSpecification\ApiComponents\ComponentsParameter\Parameter\ParameterIsRequired;
use App\OpenApiSpecification\ApiComponents\ComponentsParameter\Parameter\ParameterKey;
use App\OpenApiSpecification\ApiComponents\ComponentsParameter\Parameter\ParameterLocation;
use App\OpenApiSpecification\ApiComponents\ComponentsParameter\Parameter\ParameterName;
use App\OpenApiSpecification\ApiComponents\ComponentsParameter\Parameter\ParameterStyle;
use App\OpenApiSpecification\ApiComponents\Schema\IntegerSchema;
use App\OpenApiSpecification\ApiException\SpecificationException;

final class IntegerParameter extends Parameter
{
    private static function generate(string $name, ParameterLocation $location): self
    {
        return new self(
            ParameterName::fromString($name),
            $location,
            IntegerSchema::generate(),
            $location->isInPath() ? ParameterIsRequired::generateTrue() : ParameterIsRequired::generateFalse(),
            ParameterIsDeprecated::generateFalse()
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
            $this->schema,
            $this->isRequired,
            $this->isDeprecated,
            $this->description,
            $this->key,
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
            $this->schema,
            $this->isRequired,
            $this->isDeprecated,
            $this->description,
            $this->key,
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
            $this->schema,
            $this->isRequired,
            $this->isDeprecated,
            $this->description,
            $this->key,
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
        throw SpecificationException::generateStyleNotSupportedForType(
            ParameterStyle::DEEP_OBJECT,
            $this->getParameterType()
        );
    }

    public static function generateInCookie(string $name, ?string $docName = null): self
    {
        return self::generate($name, ParameterLocation::generateCookie());
    }

    public static function generateInQuery(string $name, ?string $docName = null): self
    {
        return self::generate($name, ParameterLocation::generateQuery());
    }

    public static function generateInHeader(string $name, ?string $docName = null): self
    {
        return self::generate($name, ParameterLocation::generateHeader());
    }

    public static function generateInPath(string $name, ?string $docName = null): self
    {
        return self::generate($name, ParameterLocation::generatePath());
    }

    public function setFormat(string $format): self
    {
        return new self(
            $this->name,
            $this->location,
            $this->schema->setFormat($format),
            $this->isRequired,
            $this->isDeprecated,
            $this->description,
            $this->key,
            $this->style,
            $this->example,
            $this->examples
        );
    }

    public function require(): self
    {
        return new self(
            $this->name,
            $this->location,
            $this->schema,
            ParameterIsRequired::generateTrue(),
            $this->isDeprecated,
            $this->description,
            $this->key,
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
            $this->schema,
            $this->isRequired,
            ParameterIsDeprecated::generateTrue(),
            $this->description,
            $this->key,
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
            $this->schema,
            $this->isRequired,
            $this->isDeprecated,
            ParameterDescription::fromString($description),
            $this->key,
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
            $this->schema->makeNullable(),
            $this->isRequired,
            $this->isDeprecated,
            $this->description,
            $this->key,
            $this->style,
            $this->example,
            $this->examples
        );
    }

    public function setMinimum(int $minimum): self
    {
        return new self(
            $this->name,
            $this->location,
            $this->schema->setMinimum($minimum),
            $this->isRequired,
            $this->isDeprecated,
            $this->description,
            $this->key,
            $this->style,
            $this->example,
            $this->examples
        );
    }

    public function setMaximum(int $maximum): self
    {
        return new self(
            $this->name,
            $this->location,
            $this->schema->setMaximum($maximum),
            $this->isRequired,
            $this->isDeprecated,
            $this->description,
            $this->key,
            $this->style,
            $this->example,
            $this->examples
        );
    }

    public function setKey(string $key): self
    {
        return new self(
            $this->name,
            $this->location,
            $this->schema,
            $this->isRequired,
            $this->isDeprecated,
            $this->description,
            ParameterKey::fromString($key),
            $this->style,
            $this->example,
            $this->examples
        );
    }

    public function setExample(ComponentsExample $example): self
    {
        return new self(
            $this->name,
            $this->location,
            $this->schema,
            $this->isRequired,
            $this->isDeprecated,
            $this->description,
            $this->key,
            $this->style,
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
            $this->name,
            $this->location,
            $this->schema,
            $this->isRequired,
            $this->isDeprecated,
            $this->description,
            $this->key,
            $this->style,
            null,
            $examples->addExample($example, $example->getName()->toString())
        );
    }

    public function getRouteRequirements(): ?string
    {
        $minimum = $this->schema->getMinimum();

        if (!$minimum || $minimum->toInt() < 0) {
            return '^(\-?)\d*';
        }

        if ($minimum->toInt() > 0) {
            return '^[1-9]\d*';
        }

        return '^\d*';
    }
}