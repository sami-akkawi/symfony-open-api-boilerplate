<?php declare(strict_types=1);

namespace App\OpenApiSpecification\ApiComponents\Parameter;

use App\OpenApiSpecification\ApiComponents\Example;
use App\OpenApiSpecification\ApiComponents\Examples;
use App\OpenApiSpecification\ApiComponents\Schema;
use App\OpenApiSpecification\ApiException\SpecificationException;

abstract class SchemaParameter extends DetailedParameter
{
    private const PRIMITIVE_PARAMETERS = [
        BooleanParameter::class,
        IntegerParameter::class,
        NumberParameter::class,
        StringParameter::class
    ];

    protected Schema $schema;
    protected ?ParameterStyle $style;

    protected function __construct(
        ParameterName $name,
        ParameterLocation $location,
        ParameterIsRequired $isRequired,
        ParameterIsDeprecated $isDeprecated,
        Schema $schema,
        ?ParameterDescription $description = null,
        ?ParameterDocName $docName = null,
        ?ParameterStyle $style = null,
        ?Example $example = null,
        ?Examples $examples = null
    ) {
        parent::__construct($name, $location, $isRequired, $isDeprecated, $description, $docName, $example, $examples);
        $this->schema = $schema;
        $this->style = $style;
    }

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

    public function toOpenApiSpecification(): array
    {
        $this->validateStyle();

        $specification = [
            'name' => $this->name->toString(),
            'in' => $this->location->toString(),
            'schema' => $this->schema->toOpenApiSpecification()
        ];

        if ($this->isRequired->toBool()) {
            $specification['required'] = true;
        }

        if ($this->style) {
            $specification['style'] = $this->style->toString();
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
}