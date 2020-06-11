<?php declare(strict=1);

namespace App\ApiV1Bundle\ApiSpecification\ApiComponents\Parameter;

use App\ApiV1Bundle\ApiSpecification\ApiComponents\RequestContent\ContentMediaType;
use App\ApiV1Bundle\ApiSpecification\ApiComponents\RequestContent\ContentMediaTypes;
use App\ApiV1Bundle\ApiSpecification\ApiException\SpecificationException;

final class ContentParameter extends DetailedParameter
{
    private ContentMediaTypes $mediaTypes;

    private function __construct(
        ParameterName $name,
        ParameterLocation $location,
        ContentMediaTypes $mediaTypes,
        ?ParameterIsRequired $isRequired = null,
        ?ParameterIsDeprecated $isDeprecated = null,
        ?ParameterDescription $description = null,
        ?ParameterDocName $docName = null
    ) {
        parent::__construct($name, $location, $isRequired, $isDeprecated, $description, $docName);
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
            ParameterDocName::fromString($name)
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
            $this->docName
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
            $this->docName
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
            ContentMediaTypes::generate()
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
            $this->docName
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

    public function addMediaType(ContentMediaType $mediaType): self
    {
        return new self(
            $this->name,
            $this->location,
            $this->mediaTypes->addMediaType($mediaType),
            $this->isRequired,
            $this->isDeprecated,
            $this->description,
            $this->docName
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

        return $specification;
    }
}