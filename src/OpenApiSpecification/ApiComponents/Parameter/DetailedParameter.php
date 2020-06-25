<?php declare(strict_types=1);

namespace App\OpenApiSpecification\ApiComponents\Parameter;

use App\Message\FieldMessage;
use App\OpenApiSpecification\ApiComponents\ComponentsExample;
use App\OpenApiSpecification\ApiComponents\ComponentsExamples;
use App\OpenApiSpecification\ApiComponents\Parameter;
use App\OpenApiSpecification\ApiComponents\Schema;
use App\OpenApiSpecification\ApiException\SpecificationException;

/**
 * Describes a single operation parameter.
 * A unique parameter is defined by a combination of a name and location.
 * http://spec.openapis.org/oas/v3.0.3#parameter-object
 */

abstract class DetailedParameter extends Parameter
{
    private const PRIMITIVE_PARAMETERS = [
        BooleanParameter::class,
        IntegerParameter::class,
        NumberParameter::class,
        StringParameter::class
    ];

    private const INVALID_NAMES_IN_HEADER = ['Accept', 'Content-Type', 'Authorization'];

    protected ParameterName $name;
    protected ParameterLocation $location;
    protected ParameterIsRequired $isRequired;
    protected ParameterIsDeprecated $isDeprecated;
    protected ?ParameterDescription $description;
    protected ?ComponentsExample $example;
    protected ?ComponentsExamples $examples;
    protected Schema $schema;
    protected ?ParameterStyle $style;

    protected function __construct(
        ParameterName $name,
        ParameterLocation $location,
        Schema $schema,
        ?ParameterIsRequired $isRequired = null,
        ?ParameterIsDeprecated $isDeprecated = null,
        ?ParameterDescription $description =null,
        ?ParameterDocName $docName = null,
        ?ParameterStyle $style = null,
        ?ComponentsExample $example = null,
        ?ComponentsExamples $examples = null
    ) {
        if ($location->isInHeader() && in_array($name->toString(), self::INVALID_NAMES_IN_HEADER)) {
            throw SpecificationException::generateInvalidNameInHeader($name->toString());
        }

        $this->name = $name;
        $this->location = $location;
        $this->schema = $schema;
        $this->isRequired = $isRequired ?? ParameterIsRequired::generateFalse();
        $this->isDeprecated = $isDeprecated ?? ParameterIsDeprecated::generateFalse();
        $this->description = $description;
        $this->docName = $docName;
        $this->example = $example;
        $this->examples = $examples;
        $this->style = $style;
    }

    public function isRequired(): bool
    {
        return $this->isRequired->toBool();
    }

    public function getName(): ParameterName
    {
        return $this->name;
    }

    public function getLocation(): ParameterLocation
    {
        return $this->location;
    }

    public function isInPath(): bool
    {
        return $this->location->isInPath();
    }

    public function toDetailedParameter(): self
    {
        return $this;
    }

    public function isIdenticalTo(self $parameter): bool
    {
        return (
            $this->getName()->isIdenticalTo($parameter->getName())
            && $this->getLocation()->isIdenticalTo($parameter->getLocation())
        );
    }

    public abstract static function generateInQuery(string $name);

    public abstract static function generateInHeader(string $name);

    public abstract static function generateInPath(string $name);

    public abstract static function generateInCookie(string $name);

    public abstract function require();

    public abstract function deprecate();

    public abstract function setDescription(string $description);

    public abstract function addExample(ComponentsExample $example);

    public abstract function setExample(ComponentsExample $example);

    public abstract function makeNullable();

    public abstract function styleAsMatrix();

    public abstract function styleAsLabel();

    public abstract function styleAsForm();

    public abstract function styleAsSimple();

    public abstract function styleAsSpaceDelimited();

    public abstract function styleAsPipeDelimited();

    public abstract function styleAsDeepObject();

    public function getStyle(): ?ParameterStyle
    {
        return $this->style;
    }

    protected function validateStyle(): void
    {
        $style = $this->getStyle();
        if (is_null($style)) {
            return;
        }

        $style = $style->toString();

        if (!in_array($style, array_keys(ParameterStyle::VALID_USAGES))) {
            throw SpecificationException::generateInvalidStyle($style);
        }

        $parameterType = $this->getParameterType();

        if (!in_array($parameterType, ParameterStyle::VALID_USAGES[$style]['types'])) {
            throw SpecificationException::generateStyleNotSupportedForType($style, $parameterType);
        }

        $location = $this->getLocation()->toString();

        if (!in_array($location, ParameterStyle::VALID_USAGES[$style]['locations'])) {
            throw SpecificationException::generateStyleNotSupportedForLocation($style, $location);
        }
    }

    protected function getParameterType(): string
    {
        if ($this->isPrimitiveParameter()) {
            return 'primitive';
        } elseif ($this->isArrayParameter()) {
            return 'array';
        } elseif ($this->isObjectParameter()) {
            return 'object';
        } else {
            return 'undefined';
        }
    }

    private function isArrayParameter(): bool
    {
        return static::class === ArrayParameter::class;
    }

    private function isObjectParameter(): bool
    {
        return static::class === ObjectParameter::class;
    }

    private function isPrimitiveParameter(): bool
    {
        return in_array(static::class, self::PRIMITIVE_PARAMETERS);
    }

    private function isBooleanParameter(): bool
    {
        return static::class === BooleanParameter::class;
    }

    private function isNumberParameter(): bool
    {
        return static::class === NumberParameter::class;
    }

    private function isIntegerParameter(): bool
    {
        return static::class === IntegerParameter::class;
    }

    private function isStringParameter(): bool
    {
        return static::class === StringParameter::class;
    }

    public function toOpenApiSpecification(): array
    {
        $this->validateStyle();

        $specification = [
            'name' => $this->name->toString(),
            'in' => $this->location->toString(),
            'schema' => $this->schema->toOpenApiSpecification()
        ];

        if ($this->style) {
            $specification['style'] = $this->style->toString();
        }

        if ($this->isRequired->toBool()) {
            $specification['required'] = true;
        }

        if ($this->isDeprecated->toBool()) {
            $specification['deprecated'] = true;
        }

        if ($this->description) {
            $specification['description'] = $this->description->toString();
        }

        if ($this->example) {
            $specification['example'] = $this->example->toOpenApiSpecification();
        }

        if ($this->examples) {
            $specification['examples'] = $this->examples->toOpenApiSpecification();
        }

        return $specification;
    }

    public function getRouteRequirements(): ?string
    {
        $schemaType = $this->schema->getType();

        if (!$schemaType || !$schemaType->isCompatibleWithRoute()) {
            throw new SpecificationException('schema not compatible with route');
        }

        return null;
    }

    public function isValueValid($value): array
    {
        $parameterErrors = [];
        $errors = $this->schema->isValueValid($value);
        foreach ($errors as $error) {
            if ($error instanceof FieldMessage) {
                $parameterErrors[] = $error->prependPath($this->name->toString());
                continue;
            }
            $parameterErrors[] = FieldMessage::generate([$this->name->toString()], $error);
        }
        return $parameterErrors;
    }

    public function getSchema(): Schema
    {
        return $this->schema;
    }
}