<?php declare(strict_types=1);

namespace App\OpenApiSpecification\ApiComponents\ComponentsHeader;

use App\OpenApiSpecification\ApiComponents\ComponentsExample;
use App\OpenApiSpecification\ApiComponents\ComponentsExamples;
use App\OpenApiSpecification\ApiComponents\ComponentsHeader\Header\HeaderIsRequired;
use App\OpenApiSpecification\ApiComponents\ComponentsHeader\Header\HeaderIsDeprecated;
use App\OpenApiSpecification\ApiComponents\ComponentsHeader\Header\HeaderDescription;
use App\OpenApiSpecification\ApiComponents\ComponentsHeader\Header\HeaderKey;
use App\OpenApiSpecification\ApiComponents\ComponentsMediaType;
use App\OpenApiSpecification\ApiComponents\ComponentsMediaTypes;
use App\OpenApiSpecification\ApiException\SpecificationException;

final class ContentHeader extends DetailedHeader
{
    private ComponentsMediaTypes $mediaTypes;

    private function __construct(
        ComponentsMediaTypes $mediaTypes,
        ?HeaderIsRequired $isRequired = null,
        ?HeaderIsDeprecated $isDeprecated = null,
        ?HeaderDescription $description = null,
        ?HeaderKey $key = null,
        ?ComponentsExample $example = null,
        ?ComponentsExamples $examples = null
    ) {
        parent::__construct($isRequired, $isDeprecated, $description, $key, $example, $examples);
        $this->mediaTypes = $mediaTypes;
    }

    public function setKey(string $key): self
    {
        return new self(
            $this->mediaTypes,
            $this->isRequired,
            $this->isDeprecated,
            $this->description,
            HeaderKey::fromString($key),
            $this->example,
            $this->examples
        );
    }

    public function setDescription(string $description): self
    {
        return new self(
            $this->mediaTypes,
            $this->isRequired,
            $this->isDeprecated,
            HeaderDescription::fromString($description),
            $this->key,
            $this->example,
            $this->examples
        );
    }

    public function deprecate(): self
    {
        return new self(
            $this->mediaTypes,
            $this->isRequired,
            HeaderIsDeprecated::generateTrue(),
            $this->description,
            $this->key,
            $this->example,
            $this->examples
        );
    }

    public static function generate(): self
    {
        return new self(ComponentsMediaTypes::generate());
    }

    public function require(): self
    {
        return new self(
            $this->mediaTypes,
            HeaderIsRequired::generateTrue(),
            $this->isDeprecated,
            $this->description,
            $this->key,
            $this->example,
            $this->examples
        );
    }

    public function addMediaType(ComponentsMediaType $mediaType): self
    {
        return new self(
            $this->mediaTypes->addMediaType($mediaType),
            $this->isRequired,
            $this->isDeprecated,
            $this->description,
            $this->key,
            $this->example,
            $this->examples
        );
    }

    private function hasMediaTypes(): bool
    {
        return $this->mediaTypes->hasValues();
    }

    public function toOpenApiSpecification(): array
    {
        if (!$this->hasMediaTypes()) {
            throw SpecificationException::generateMediaTypesMustBeDefined();
        }

        $specification = [
            'content' => $this->mediaTypes->toOpenApiSpecification()
        ];

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
            $specification['example'] = $this->example->getLiteralValue();
        }

        if ($this->examples) {
            $specification['examples'] = $this->examples->toOpenApiSpecification();
        }

        return $specification;
    }

    public function setExample(ComponentsExample $example): self
    {
        return new self(
            $this->mediaTypes,
            $this->isRequired,
            $this->isDeprecated,
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
            $this->mediaTypes,
            $this->isRequired,
            $this->isDeprecated,
            $this->description,
            $this->key,
            null,
            $examples->addExample($example, $example->getName()->toString())
        );
    }
}