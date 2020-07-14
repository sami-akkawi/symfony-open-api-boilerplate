<?php declare(strict_types=1);

namespace App\OpenApiSpecification\ApiComponents;

use App\OpenApiSpecification\ApiComponents\ComponentsMediaType\MediaTypeMimeType;
use App\OpenApiSpecification\ApiComponents\ComponentsSchema\Schema;
use App\OpenApiSpecification\ApiException\SpecificationException;

final class ComponentsMediaType
{
    private MediaTypeMimeType $mimeType;
    private ComponentsSchema $schema;
    private ?ComponentsExample $example;
    private ?ComponentsExamples $examples;

    private function __construct(
        MediaTypeMimeType $mimeType,
        ComponentsSchema $schema,
        ?ComponentsExample $example = null,
        ?ComponentsExamples $examples = null
    ) {
        $this->mimeType = $mimeType;
        $this->schema = $schema;
        $this->example = $example;
        $this->examples = $examples;
    }

    private function isXml(): bool
    {
        return $this->mimeType->isXml();
    }

    public static function generateJson(ComponentsSchema $schema): self
    {
        return new self(MediaTypeMimeType::generateJson(), $schema);
    }

    public static function generateXml(ComponentsSchema $schema): self
    {
        return new self(MediaTypeMimeType::generateXml(), $schema);
    }

    public function getSchema(): Schema
    {
        return $this->schema->toSchema();
    }

    public function isValueValid($value): array
    {
        return $this->schema->isValueValid($value);
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

        return new self($this->mimeType, $this->schema, null, $examples->addExample($example, $example->getName()->toString()));
    }

    public function setExample(ComponentsExample $example): self
    {
        return new self($this->mimeType, $this->schema, $example, null);
    }


    public function getMimeType(): MediaTypeMimeType
    {
        return $this->mimeType;
    }

    public function toOpenApiSpecification(): array
    {
        $specification = [
            'schema' => $this->isXml() ? $this->schema->toXmlOpenApiSpecification() : $this->schema->toOpenApiSpecification()
        ];
        if ($this->example) {
            $specification['example'] = $this->example->getLiteralValue();
        }
        if ($this->examples) {
            $specification['examples'] = $this->examples->toOpenApiSpecification();
        }
        return $specification;
    }
}