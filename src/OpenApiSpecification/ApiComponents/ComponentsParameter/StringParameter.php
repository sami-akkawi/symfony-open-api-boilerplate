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
use App\OpenApiSpecification\ApiComponents\ComponentsSchema\StringSchema;
use App\OpenApiSpecification\ApiException\SpecificationException;

final class StringParameter extends Parameter
{
    private static function generate(string $name, ParameterLocation $location): self
    {
        return new self(
            ParameterName::fromString($name),
            $location,
            StringSchema::generate(),
            $location->isInPath() ? ParameterIsRequired::generateTrue() : ParameterIsRequired::generateFalse(),
            ParameterIsDeprecated::generateFalse()
        );
    }

    private static function generateUuid(string $name, ParameterLocation $location): self
    {
        return new self(
            ParameterName::fromString($name),
            $location,
            StringSchema::generateUuid(),
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

        $this->style = ParameterStyle::generateMatrix();
        return $this;
    }

    public function styleAsLabel(): self
    {
        if (!$this->location->isInPath()) {
            throw SpecificationException::generateStyleNotSupportedForLocation(
                ParameterStyle::LABEL,
                $this->location->toString()
            );
        }

        $this->style = ParameterStyle::generateLabel();
        return $this;
    }

    public function styleAsForm(): self
    {
        if (!$this->location->isInQuery() && !$this->location->isInCookie()) {
            throw SpecificationException::generateStyleNotSupportedForLocation(
                ParameterStyle::FORM,
                $this->location->toString()
            );
        }

        $this->style = ParameterStyle::generateForm();
        return $this;
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

    public static function generateInCookie(string $name): self
    {
        return self::generate($name, ParameterLocation::generateCookie());
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
        return self::generate($name, ParameterLocation::generatePath());
    }

    public static function generateUuidInPath(string $name): self
    {
        return self::generateUuid($name, ParameterLocation::generatePath());
    }

    public function setFormat(string $format): self
    {
        $this->schema = $this->schema->setFormat($format);
        return $this;
    }

    /** @param string[] $options */
    public function setOptions(array $options): self
    {
        $this->schema = $this->schema->setOptions($options);
        return $this;
    }

    public function makeNullable(): self
    {
        $this->schema = $this->schema->makeNullable();
        return $this;
    }

    public function require(): self
    {
        $this->isRequired = ParameterIsRequired::generateTrue();
        return $this;
    }

    public function deprecate(): self
    {
        $this->isDeprecated = ParameterIsDeprecated::generateTrue();
        return $this;
    }

    public function setDescription(string $description): self
    {
        $this->description = ParameterDescription::fromString($description);
        return $this;
    }

    public function setKey(string $key): self
    {
        $this->key = ParameterKey::fromString($key);
        return $this;
    }

    public function setMinimumLength(int $minLength): self
    {
        $this->schema = $this->schema->setMinimumLength($minLength);
        return $this;
    }

    public function setMaximumLength(int $maxLength): self
    {
        $this->schema = $this->schema->setMaximumLength($maxLength);
        return $this;
    }

    public function setExample(ComponentsExample $example): self
    {
        $this->example = $example;
        $this->examples = null;
        return $this;
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
        if ($this->example) {
            $examples = ComponentsExamples::generate()->addExample($this->example, $this->example->getName()->toString());
            $this->example = null;
        }
        $this->examples = $examples->addExample($example, $example->getName()->toString());

        return $this;
    }

    public function getRouteRequirements(): ?string
    {
        $schemaType = $this->schema->getType();

        if ($schemaType->isStringUuid()) {
            return '[a-f\d]{8}(\-[a-f\d]{4}){3}\-[a-f\d]{12}';
        }

        if ($schemaType->isStringDate()) {
            return '[12]\d{3}\-(0[1-9]|1[0-2])\-(0[1-9]|[12]\d|3[01])';
        }

        return null;
    }
}