<?php declare(strict=1);

namespace App\ApiV1Bundle\ApiSpecification\ApiComponents\Parameter;


use App\ApiV1Bundle\ApiSpecification\ApiComponents\Example;
use App\ApiV1Bundle\ApiSpecification\ApiComponents\Examples;
use App\ApiV1Bundle\ApiSpecification\ApiComponents\MediaType;
use App\ApiV1Bundle\ApiSpecification\ApiComponents\MediaTypes;
use App\ApiV1Bundle\ApiSpecification\ApiException\SpecificationException;

final class ContentParameter extends DetailedParameter
{
    private MediaTypes $mediaTypes;

    private function __construct(
        ParameterName $name,
        ParameterLocation $location,
        MediaTypes $mediaTypes,
        ?ParameterIsRequired $isRequired = null,
        ?ParameterIsDeprecated $isDeprecated = null,
        ?ParameterDescription $description = null,
        ?ParameterDocName $docName = null,
        ?Example $example = null,
        ?Examples $examples = null
    ) {
        parent::__construct($name, $location, $isRequired, $isDeprecated, $description, $docName, $example, $examples);
        $this->mediaTypes = $mediaTypes;
    }

    public function setDocName(string $name): self
    {
        return new self(
            $this->name,
            $this->location,
            $this->mediaTypes,
            $this->isRequired,
            $this->isDeprecated,
            $this->description,
            ParameterDocName::fromString($name),
            $this->example,
            $this->examples
        );
    }

    public function setDescription(string $description): self
    {
        return new self(
            $this->name,
            $this->location,
            $this->mediaTypes,
            $this->isRequired,
            $this->isDeprecated,
            ParameterDescription::fromString($description),
            $this->docName,
            $this->example,
            $this->examples
        );
    }

    public function deprecate(): self
    {
        return new self(
            $this->name,
            $this->location,
            $this->mediaTypes,
            $this->isRequired,
            ParameterIsDeprecated::generateTrue(),
            $this->description,
            $this->docName,
            $this->example,
            $this->examples
        );
    }

    private static function generate(string $name, ParameterLocation $location): self
    {
        if ($location->isInPath()) {
            throw SpecificationException::generateCannotBeInPath('ContentParameter');
        }

        return new self(
            ParameterName::fromString($name),
            $location,
            MediaTypes::generate()
        );
    }

    public static function generateInPath(string $name): self
    {
        throw SpecificationException::generateCannotBeInPath('ContentParameter');
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
            $this->mediaTypes,
            ParameterIsRequired::generateTrue(),
            $this->isDeprecated,
            $this->description,
            $this->docName,
            $this->example,
            $this->examples
        );
    }

    public static function generateInHeader(string $name): self
    {
        return self::generate($name, ParameterLocation::generateHeader());
    }

    public static function generateInQuery(string $name): self
    {
        return self::generate($name, ParameterLocation::generateQuery());
    }

    public function addMediaType(MediaType $mediaType): self
    {
        return new self(
            $this->name,
            $this->location,
            $this->mediaTypes->addMediaType($mediaType),
            $this->isRequired,
            $this->isDeprecated,
            $this->description,
            $this->docName,
            $this->example,
            $this->examples
        );
    }

    public function setExample(Example $example): self
    {
        return new self(
            $this->name,
            $this->location,
            $this->mediaTypes,
            $this->isRequired,
            $this->isDeprecated,
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
            $this->name,
            $this->location,
            $this->mediaTypes,
            $this->isRequired,
            $this->isDeprecated,
            $this->description,
            $this->docName,
            null,
            $examples->addExample($example, $example->getName()->toString())
        );
    }

    public function toOpenApiSpecification(): array
    {
        $specification = [
            'name' => $this->name->toString(),
            'in' => $this->location->toString(),
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
            $specification['example'] = $this->example->toOpenApiSpecification();
        }

        if ($this->examples) {
            $specification['examples'] = $this->examples->toOpenApiSpecification();
        }

        return $specification;
    }
}